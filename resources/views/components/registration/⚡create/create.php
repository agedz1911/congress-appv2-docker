<?php

use App\Models\Participant;
use App\Models\Product\Product;
use App\Models\Transaction\Order;
use App\Models\Transaction\OrderItem;
use App\Models\Transaction\Payment;
use RuntimeException;
use Throwable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component
{
    public array $cart = [];
    public string $currentStep = 'cart';
    public string $participantSearch = '';
    public ?string $selectedParticipantId = null;
    public string $paymentMethod = 'online';
    public ?int $completedOrderId = null;
    public ?string $completedOrderNumber = null;
    public ?string $completedTransactionId = null;

    public function mount(?string $product = null): void
    {
        $product ??= request()->query('product');

        $this->cart = session()->get('registration_cart', []);
        $this->selectedParticipantId = session()->get('registration_selected_participant_id');
        $this->completedOrderId = session()->get('registration_completed_order_id');
        $this->completedOrderNumber = session()->get('registration_completed_order_number');
        $this->completedTransactionId = session()->get('registration_completed_transaction_id');

        if (filled($product)) {
            $this->addToCart($product);
        }

        if ($this->completedOrderId) {
            $this->currentStep = 'finish';
        }
    }

    public function addToCart(string $productId): void
    {
        $product = $this->findActiveProduct($productId);

        if (! $product) {
            $this->dispatch('cart-feedback', type: 'error', message: 'Produk tidak tersedia untuk periode registrasi saat ini.');

            return;
        }

        $currentQty = (int) ($this->cart[$productId] ?? 0);

        if ($product->stock > 0 && $currentQty >= $product->stock) {
            $this->dispatch('cart-feedback', type: 'warning', message: 'Stok produk sudah mencapai batas maksimum.');

            return;
        }

        $this->cart[$productId] = $currentQty + 1;

        $this->persistCart();

        $this->dispatch('cart-feedback', type: 'success', message: 'Produk berhasil ditambahkan ke cart.');
        $this->dispatch('scroll-to', target: 'cart-section');
    }

    public function increaseQty(string $productId): void
    {
        $this->addToCart($productId);
    }

    public function decreaseQty(string $productId): void
    {
        if (! isset($this->cart[$productId])) {
            return;
        }

        $nextQty = (int) $this->cart[$productId] - 1;

        if ($nextQty <= 0) {
            unset($this->cart[$productId]);
        } else {
            $this->cart[$productId] = $nextQty;
        }

        $this->persistCart();

        $this->dispatch('cart-feedback', type: 'info', message: 'Jumlah item berhasil diperbarui.');
    }

    public function removeItem(string $productId): void
    {
        if (! isset($this->cart[$productId])) {
            return;
        }

        unset($this->cart[$productId]);

        $this->persistCart();

        $this->dispatch('cart-feedback', type: 'info', message: 'Item dihapus dari cart.');

        if (! $this->hasCart) {
            $this->currentStep = 'cart';
        }
    }

    public function setStep(string $step): void
    {
        if (! in_array($step, ['cart', 'participant', 'payment', 'finish'], true)) {
            return;
        }

        if (in_array($step, ['participant', 'payment'], true) && ! $this->hasCart) {
            $this->dispatch('cart-feedback', type: 'warning', message: 'Tambahkan minimal 1 produk sebelum lanjut ke step berikutnya.');

            return;
        }

        if ($step === 'payment' && ! $this->selectedParticipant) {
            $this->dispatch('cart-feedback', type: 'warning', message: 'Pilih participant terlebih dahulu sebelum lanjut ke pembayaran.');

            return;
        }

        if ($step === 'finish' && ! $this->completedOrderId) {
            $this->dispatch('cart-feedback', type: 'warning', message: 'Konfirmasi pembayaran terlebih dahulu untuk melanjutkan ke finish.');

            return;
        }

        $this->currentStep = $step;
        $this->dispatch('scroll-to', target: $step . '-section');
    }

    public function confirmPayment(): void
    {
        if ($this->completedOrderId) {
            $this->currentStep = 'finish';
            $this->dispatch('scroll-to', target: 'finish-section');

            return;
        }

        if (! $this->hasCart) {
            $this->dispatch('cart-feedback', type: 'warning', message: 'Cart masih kosong. Tambahkan produk sebelum konfirmasi pembayaran.');

            return;
        }

        if (! $this->selectedParticipant) {
            $this->dispatch('cart-feedback', type: 'warning', message: 'Pilih participant terlebih dahulu sebelum konfirmasi pembayaran.');

            return;
        }

        if (! in_array($this->paymentMethod, ['online', 'bank_transfer'], true)) {
            $this->dispatch('cart-feedback', type: 'warning', message: 'Pilih metode pembayaran yang valid.');

            return;
        }

        $cartItems = $this->cartItems;

        if ($cartItems->isEmpty()) {
            $this->dispatch('cart-feedback', type: 'warning', message: 'Item cart tidak valid. Silakan cek ulang cart Anda.');

            return;
        }

        try {
            $createdOrder = DB::transaction(function () use ($cartItems) {
                $order = Order::query()->create([
                    'participant_id' => $this->selectedParticipantId,
                    'user_id' => Auth::id(),
                    'total_amount' => $this->subTotal,
                    'discount_amount' => 0,
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'payment_method' => $this->paymentMethod,
                    'payment_date' => now(),
                ]);

                foreach ($cartItems as $item) {
                    $updatedRows = Product::query()
                        ->whereKey($item['id'])
                        ->where('stock', '>=', $item['quantity'])
                        ->decrement('stock', $item['quantity']);

                    if ($updatedRows === 0) {
                        throw new RuntimeException('Stok produk tidak mencukupi untuk menyelesaikan checkout.');
                    }

                    OrderItem::query()->create([
                        'order_id' => $order->id,
                        'product_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'item_price' => $item['unit_price'],
                    ]);
                }

                $transactionId = $this->generateTransactionId();

                Payment::query()->create([
                    'order_id' => $order->id,
                    'transaction_id' => $transactionId,
                    'payment_method' => $this->paymentMethod,
                    'amount' => $this->grandTotal,
                    'status' => 'pending',
                    'payment_date' => now(),
                    'attachment' => null,
                    'kurs' => 'IDR',
                ]);

                $order->setAttribute('transaction_id', $transactionId);

                return $order;
            });
        } catch (Throwable $throwable) {
            report($throwable);
            $this->dispatch('cart-feedback', type: 'error', message: $throwable instanceof RuntimeException ? $throwable->getMessage() : 'Checkout gagal diproses. Silakan coba lagi.');

            return;
        }

        $this->completedOrderId = $createdOrder->id;
        $this->completedOrderNumber = $createdOrder->order_number;
        $this->completedTransactionId = $createdOrder->transaction_id;
        $this->currentStep = 'finish';

        session()->put('registration_completed_order_id', $this->completedOrderId);
        session()->put('registration_completed_order_number', $this->completedOrderNumber);
        session()->put('registration_completed_transaction_id', $this->completedTransactionId);
        session()->forget('registration_cart');

        $this->cart = [];
        $this->dispatch('cart-feedback', type: 'success', message: 'Checkout berhasil. Data order, item, dan payment sudah tersimpan.');
        $this->dispatch('scroll-to', target: 'finish-section');
    }

    public function selectParticipant(string $participantId): void
    {
        $participant = $this->participants->firstWhere('id', $participantId);

        if (! $participant) {
            $this->dispatch('cart-feedback', type: 'error', message: 'Participant tidak ditemukan untuk akun Anda.');

            return;
        }

        $this->selectedParticipantId = $participantId;
        session()->put('registration_selected_participant_id', $participantId);

        $this->dispatch('cart-feedback', type: 'success', message: 'Participant berhasil dipilih.');
    }

    public function clearSelectedParticipant(): void
    {
        $this->selectedParticipantId = null;
        session()->forget('registration_selected_participant_id');

        if ($this->currentStep === 'payment') {
            $this->currentStep = 'participant';
        }
    }

    public function getAvailableProductsProperty(): Collection
    {
        $today = today()->toDateString();

        return Product::query()
            ->with('categoryProduct')
            ->whereDate('date_start', '<=', $today)
            ->whereDate('date_end', '>=', $today)
            ->orderBy('date_start', 'asc')
            ->get();
    }

    public function getCartItemsProperty(): Collection
    {
        if (empty($this->cart)) {
            return collect();
        }

        $products = Product::query()
            ->with('categoryProduct')
            ->whereIn('id', array_keys($this->cart))
            ->get()
            ->keyBy('id');

        return collect($this->cart)
            ->map(function (int $quantity, string $productId) use ($products): ?array {
                $product = $products->get($productId);

                if (! $product) {
                    return null;
                }

                $unitPrice = $product->is_discounted && filled($product->discounted_price)
                    ? (int) $product->discounted_price
                    : (int) $product->price;

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'type_product' => $product->type_product,
                    'category' => $product->categoryProduct?->name,
                    'is_discounted' => (bool) $product->is_discounted,
                    'price' => (int) $product->price,
                    'discounted_price' => $product->discounted_price ? (int) $product->discounted_price : null,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'line_total' => $unitPrice * $quantity,
                ];
            })
            ->filter()
            ->values();
    }

    public function getTotalItemsProperty(): int
    {
        return (int) collect($this->cart)->sum();
    }

    public function getSubTotalProperty(): int
    {
        return (int) $this->cartItems->sum('line_total');
    }

    public function getHasCartProperty(): bool
    {
        return $this->totalItems > 0;
    }

    public function getParticipantsProperty(): Collection
    {
        $userId = Auth::id();

        if (! $userId) {
            return collect();
        }

        return Participant::query()
            ->where('user_id', $userId)
            ->when(filled($this->participantSearch), function ($query) {
                $search = trim($this->participantSearch);

                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->where('firstname', 'like', '%' . $search . '%')
                        ->orWhere('lastname', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('institution', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('firstname')
            ->orderBy('lastname')
            ->get();
    }

    public function getSelectedParticipantProperty(): ?Participant
    {
        if (! $this->selectedParticipantId) {
            return null;
        }

        return $this->participants->firstWhere('id', $this->selectedParticipantId);
    }

    public function getSelectedParticipantNameProperty(): string
    {
        if (! $this->selectedParticipant) {
            return '-';
        }

        $firstName = trim((string) $this->selectedParticipant->firstname);
        $lastName = trim((string) $this->selectedParticipant->lastname);
        $fullName = trim($firstName . ' ' . $lastName);

        return $fullName !== '' ? $fullName : (string) $this->selectedParticipant->email;
    }

    public function getPaymentMethodLabelProperty(): string
    {
        return $this->paymentMethod === 'bank_transfer' ? 'Bank Transfer' : 'Online Payment';
    }

    public function getGrandTotalProperty(): int
    {
        return $this->subTotal;
    }

    protected function findActiveProduct(string $productId): ?Product
    {
        $today = today()->toDateString();

        return Product::query()
            ->whereKey($productId)
            ->whereDate('date_start', '<=', $today)
            ->whereDate('date_end', '>=', $today)
            ->first();
    }

    protected function persistCart(): void
    {
        session()->put('registration_cart', $this->cart);
    }

    protected function generateTransactionId(): string
    {
        do {
            $transactionId = 'trx-' . strtoupper(Str::random(10));
        } while (Payment::query()->where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }
};