<div
    x-data="{
        toastOpen: false,
        toastType: 'info',
        toastMessage: '',
        showToast(type, message) {
            this.toastType = type ?? 'info';
            this.toastMessage = message ?? '';
            this.toastOpen = true;

            setTimeout(() => {
                this.toastOpen = false;
            }, 2200);
        }
    }"
    x-on:cart-feedback.window="showToast($event.detail.type, $event.detail.message)"
    x-on:scroll-to.window="const el = document.getElementById($event.detail.target); if (el) { el.scrollIntoView({ behavior: 'smooth', block: 'start' }); }">
    <div
        x-show="toastOpen"
        x-transition.opacity.duration.250ms
        class="fixed top-20 right-6 z-50"
        style="display: none;">
        <div class="alert shadow-lg"
            :class="{
                'alert-success': toastType === 'success',
                'alert-warning': toastType === 'warning',
                'alert-error': toastType === 'error',
                'alert-info': toastType === 'info'
            }">
            <span class="text-xs font-semibold" x-text="toastMessage"></span>
        </div>
    </div>

    <div class="flex justify-center items-center gap-4 text-sm font-medium text-gray-400 mb-10">
        <ul class="steps">
            <li
                wire:click="setStep('cart')"
                class="step cursor-pointer {{ $this->currentStep === 'cart' ? 'step-primary' : ($this->hasCart ? 'step-success' : '') }}"
                data-content="{{ $this->hasCart && $this->currentStep !== 'cart' ? '✓' : '' }}">
                Cart
            </li>
            <li
                wire:click="setStep('participant')"
                class="step cursor-pointer {{ $this->currentStep === 'participant' ? 'step-primary' : (in_array($this->currentStep, ['payment'], true) ? 'step-success' : '') }}"
                data-content="{{ in_array($this->currentStep, ['payment'], true) ? '✓' : '' }}">
                Participant
            </li>
            <li
                wire:click="setStep('payment')"
                class="step cursor-pointer {{ $this->currentStep === 'payment' ? 'step-primary' : ($this->currentStep === 'finish' ? 'step-success' : '') }}"
                data-content="{{ $this->currentStep === 'finish' ? '✓' : '' }}">
                Payment
            </li>
            <li
                wire:click="setStep('finish')"
                class="step cursor-pointer {{ $this->currentStep === 'finish' ? 'step-primary' : '' }}"
                data-content="{{ $this->currentStep === 'finish' ? '✓' : '' }}">
                Finish
            </li>
        </ul>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        <div class="lg:col-span-2 space-y-5">
            <div id="cart-section" class="bg-white dark:bg-gray-800 dark:border-gray-700 rounded-lg shadow-sm border border-gray-100 p-6 scroll-mt-28">
                <h2 class="text-lg font-bold border-b border-gray-100 pb-4 mb-4">My Cart ({{ $this->totalItems }})</h2>

                @forelse ($this->cartItems as $item)
                <div class="flex flex-col sm:flex-row items-start gap-4 py-4 border-b border-gray-100 last:border-b-0">
                    <div class="grow">
                        <h3 class="font-bold text-sm text-gray-900">{{ $item['name'] }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $item['category'] ?: 'Registration' }}</p>
                        <span class="badge badge-primary badge-xs">{{ $item['type_product'] ?: 'Product' }}</span>
                        <div class="flex items-center gap-2 mt-3 text-sm">
                            @if ($item['is_discounted'] && $item['discounted_price'])
                            <span class="line-through text-error">{{ number_format($item['price'], 0, ',', '.') }}</span>
                            <span class="font-bold text-gray-950">{{ number_format($item['discounted_price'], 0, ',', '.') }}</span>
                            @else
                            <span class="font-bold text-gray-950">{{ number_format($item['unit_price'], 0, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex sm:flex-col items-end justify-between w-full sm:w-auto h-full sm:h-24 mt-4 sm:mt-0">
                        <div class="join border border-slate-500 rounded-xl text-white overflow-hidden">
                            <button wire:click="decreaseQty('{{ $item['id'] }}')" wire:loading.attr="disabled" wire:target="decreaseQty('{{ $item['id'] }}')" class="btn btn-xs join-item btn-primary px-2">
                                <i class="fa-solid fa-minus text-[10px]"></i>
                            </button>
                            <span class="px-3 py-0.5 text-xs font-semibold flex items-center bg-white text-black">{{ $item['quantity'] }}</span>
                            <button wire:click="increaseQty('{{ $item['id'] }}')" wire:loading.attr="disabled" wire:target="increaseQty('{{ $item['id'] }}')" class="btn btn-xs join-item btn-primary px-2">
                                <i class="fa-solid fa-plus text-[10px]"></i>
                            </button>
                        </div>
                        <button wire:click="removeItem('{{ $item['id'] }}')" wire:loading.attr="disabled" wire:target="removeItem('{{ $item['id'] }}')" class="text-xs text-gray-400 hover:text-red-500 mt-2 flex items-center gap-1">
                            <i class="fa-solid fa-xmark text-[10px]"></i> Remove
                        </button>
                    </div>
                </div>
                @empty
                <div class="py-8 text-sm text-gray-400 text-center border border-dashed border-gray-200 rounded-lg">
                    Cart masih kosong. Pilih produk di bawah untuk mulai registrasi.
                </div>
                @endforelse

                <h2 class="text-lg font-bold border-b border-gray-100 pb-4 mb-4 mt-8">Available Registration Product</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($this->availableProducts as $product)
                    <div class="border border-gray-100 rounded-lg p-4">
                        <span class="badge badge-xs badge-info">{{ $product->type_product ?: 'Product' }}</span>
                        <h4 class="font-bold text-base text-gray-900 mt-2">{{ $product->name }}</h4>
                        <p class="text-xs text-gray-400 mt-1">{{ $product->categoryProduct?->name ?: 'Registration' }}</p>
                        <div class="flex items-center justify-between mt-3">
                            <div class="text-sm">
                                @if ($product->is_discounted && $product->discounted_price)
                                <span class="line-through text-error">{{ number_format($product->price, 0, ',', '.') }}</span>
                                <span class="font-bold text-gray-950 ml-2">{{ number_format($product->discounted_price, 0, ',', '.') }}</span>
                                @else
                                <span class="font-bold text-gray-950">{{ number_format($product->price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                            <button wire:click="addToCart('{{ $product->id }}')" wire:loading.attr="disabled" wire:target="addToCart('{{ $product->id }}')" class="btn btn-primary btn-xs">
                                <span wire:loading.remove wire:target="addToCart('{{ $product->id }}')">Add to cart</span>
                                <span wire:loading wire:target="addToCart('{{ $product->id }}')" class="loading loading-spinner loading-xs"></span>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            @if (in_array($this->currentStep, ['participant', 'payment'], true))
            <div id="participant-section" class="bg-white dark:bg-gray-800 dark:border-gray-700 rounded-lg shadow-sm border border-gray-100 p-6 scroll-mt-28">
                <h2 class="text-lg font-bold border-b border-gray-100 pb-4 mb-4">Participant</h2>

                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 transition-all space-y-4">
                    <div class="flex items-center gap-2">
                        <label class="input input-info">
                            <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none" stroke="currentColor">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="m21 21-4.3-4.3"></path>
                                </g>
                            </svg>
                            <input type="search" wire:model.live.debounce.300ms="participantSearch" class="grow" placeholder="Search participant" />
                        </label>
                        @if ($this->selectedParticipant)
                        <button wire:click="clearSelectedParticipant" class="btn btn-outline btn-error font-bold rounded-xl px-4">Clear</button>
                        @endif
                    </div>

                    <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                        @forelse ($this->participants as $participant)
                        <button
                            type="button"
                            wire:click="selectParticipant('{{ $participant->id }}')"
                            class="w-full text-left border rounded-lg p-4 transition-all hover:border-gray-300 {{ $this->selectedParticipantId === $participant->id ? 'border-info bg-info/5' : 'border-gray-200 bg-white' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ trim($participant->firstname . ' ' . $participant->lastname) }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $participant->email }}</p>
                                    @if ($participant->institution)
                                    <p class="text-xs text-gray-400 mt-1">{{ $participant->institution }}</p>
                                    @endif
                                </div>
                                @if ($this->selectedParticipantId === $participant->id)
                                <span class="badge badge-info badge-sm">Selected</span>
                                @endif
                            </div>
                        </button>
                        @empty
                        <div class="text-sm text-gray-500 border border-dashed border-gray-300 rounded-lg p-4 text-center">
                            Participant tidak ditemukan. Silakan lengkapi data participant terlebih dahulu.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif

            @if ($this->currentStep === 'payment')
            <div id="payment-section" class="bg-white dark:bg-gray-800 dark:border-gray-700 rounded-lg shadow-sm border border-gray-100 p-6 scroll-mt-28">
                <h2 class="text-lg font-bold border-b border-gray-100 pb-4 mb-4">Payment Method</h2>
                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 hover:border-gray-300 transition-all">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="radio" wire:model.live="paymentMethod" value="online" name="payment_method" class="radio radio-sm mt-0.5 checked:bg-info [--chkbg:info]" />
                        <div class="grow">
                            <span class="block font-bold text-sm text-gray-900">Online Payment</span>
                            <span class="block text-xs text-gray-400 mt-0.5">Pay securely using Credit/Debit Card or E-Wallet.</span>
                            <div id="online-details"></div>
                        </div>
                    </label>
                </div>

                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 hover:border-gray-300 transition-all">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="radio" wire:model.live="paymentMethod" value="bank_transfer" name="payment_method" class="radio radio-sm mt-0.5 checked:bg-info [--chkbg:info]" />
                        <div class="grow">
                            <span class="block font-bold text-sm text-gray-900">Bank Transfer</span>
                            <span class="block text-xs text-gray-400 mt-0.5">Transfer directly to our corporate bank account.</span>
                            <div id="bank-details" class="mt-4 p-4 bg-white border border-gray-100 rounded space-y-3 {{ $this->paymentMethod === 'bank_transfer' ? '' : 'hidden' }}">
                                <p class="text-xs text-gray-500 font-medium">Silakan lakukan transfer ke rekening resmi berikut:</p>
                                <div class="grid grid-cols-2 gap-y-2 text-xs border-t border-gray-50 pt-2">
                                    <span class="text-gray-400">Bank Name:</span>
                                    <span class="font-bold text-gray-900 text-right">Royal Bank of Canada (RBC)</span>

                                    <span class="text-gray-400">Account Name:</span>
                                    <span class="font-bold text-gray-900 text-right">Canadian Market Inc.</span>

                                    <span class="text-gray-400">Account Number:</span>
                                    <span class="font-bold text-gray-950 text-right tracking-wider">1234-5678-9012</span>
                                </div>
                                <div class="p-2.5 bg-amber-50 border border-amber-200/70 text-amber-800 text-[11px] rounded mt-2">
                                    <i class="fa-solid fa-circle-info mr-1"></i> Mohon sertakan ID Order Anda pada berita transfer sebelum mengunggah bukti pembayaran.
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
            @endif
        </div>

        <div class="lg:sticky lg:top-24">
            <div class="bg-white dark:bg-gray-800 dark:border-gray-700 rounded-lg shadow-sm border border-gray-100 p-6 text-xs text-gray-600 space-y-4">
                <h3 class="text-sm font-bold text-gray-900 border-b border-gray-100 pb-2">Your Order</h3>

                <div class="flex justify-between">
                    <span>Subtotal ({{ $this->totalItems }} items)</span>
                    <span class="font-bold text-gray-950">{{ number_format($this->subTotal, 0, ',', '.') }}</span>
                </div>

                <div class="space-y-2 border-b border-gray-100 pb-3">
                    <div class="flex justify-between">
                        <span>Coupon</span>
                        <span class="font-bold text-gray-950">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Discount</span>
                        <span class="font-bold text-gray-950">0</span>
                    </div>
                </div>

                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <span>Participant</span>
                    <span class="font-bold text-gray-950">{{ $this->selectedParticipantName }}</span>
                </div>

                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <span>Payment Method</span>
                    <span class="font-bold text-gray-950">{{ $this->paymentMethodLabel }}</span>
                </div>

                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <span>Step</span>
                    <span class="font-bold text-gray-950 capitalize">{{ $this->currentStep }}</span>
                </div>

                <div class="flex justify-between items-center text-sm font-bold text-gray-900 pt-1">
                    <span>Grand Total</span>
                    <span class="text-base text-gray-950">{{ number_format($this->grandTotal, 0, ',', '.') }}</span>
                </div>

                @if ($this->currentStep === 'cart')
                <button
                    wire:click="setStep('participant')"
                    wire:loading.attr="disabled"
                    wire:target="setStep"
                    @disabled(! $this->hasCart)
                    class="btn w-full btn-info border-none font-bold rounded-sm mt-4 text-xs tracking-wider"
                    >
                    {{ $this->hasCart ? 'Choose Participant' : 'Pilih produk dulu' }}
                </button>
                @elseif ($this->currentStep === 'participant')
                <button
                    wire:click="setStep('payment')"
                    wire:loading.attr="disabled"
                    wire:target="setStep"
                    @disabled(! $this->selectedParticipant)
                    class="btn w-full btn-info border-none font-bold rounded-sm mt-4 text-xs tracking-wider"
                    >
                    {{ $this->selectedParticipant ? 'Choose Payment Method' : 'Pilih participant dulu' }}
                </button>
                @else
                @if ($this->currentStep === 'payment')
                <button
                    wire:click="confirmPayment"
                    wire:loading.attr="disabled"
                    wire:target="confirmPayment"
                    class="btn w-full btn-info border-none font-bold rounded-sm mt-4 text-xs tracking-wider">
                    <span wire:loading.remove wire:target="confirmPayment">Confirm & Pay now</span>
                    <span wire:loading wire:target="confirmPayment" class="loading loading-spinner loading-xs"></span>
                </button>
                @else
                <button class="btn w-full btn-success border-none font-bold rounded-sm mt-4 text-xs tracking-wider" disabled>
                    Payment Confirmed
                </button>
                @endif
                @endif
            </div>
        </div>
    </div>

    @if ($this->currentStep === 'finish')
    <div id="finish-section" class="mt-5 bg-white dark:bg-gray-800 dark:border-gray-700 rounded-lg shadow-sm border border-gray-100 p-6 scroll-mt-28">
        <h2 class="text-lg font-bold border-b border-gray-100 pb-4 mb-4">Finish</h2>

        <div class="space-y-3 text-sm">
            <div class="flex items-center justify-between border border-gray-100 rounded-lg px-4 py-3">
                <span class="text-gray-500">Order Number</span>
                <span class="font-bold text-gray-900">{{ $this->completedOrderNumber }}</span>
            </div>
            <div class="flex items-center justify-between border border-gray-100 rounded-lg px-4 py-3">
                <span class="text-gray-500">Transaction ID</span>
                <span class="font-bold text-gray-900">{{ $this->completedTransactionId }}</span>
            </div>
            <div class="flex items-center justify-between border border-gray-100 rounded-lg px-4 py-3">
                <span class="text-gray-500">Participant</span>
                <span class="font-bold text-gray-900">{{ $this->selectedParticipantName }}</span>
            </div>
            <div class="flex items-center justify-between border border-gray-100 rounded-lg px-4 py-3">
                <span class="text-gray-500">Grand Total</span>
                <span class="font-bold text-gray-900">{{ number_format($this->grandTotal, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="mt-4 p-3 rounded-lg bg-success/10 text-success text-xs font-semibold">
            Registrasi berhasil disimpan ke tabel orders, order_items, dan payments.
        </div>
    </div>
    @endif

</div>