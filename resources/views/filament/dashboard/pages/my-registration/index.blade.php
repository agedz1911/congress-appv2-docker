<x-filament-panels::page>
    @include('components.styles')


    <div class="mt-8 bg-white dark:bg-gray-800 dark:border-gray-700 rounded-lg shadow-sm border border-gray-100 p-6">
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-100 pb-4 mb-4">
            <div>
                <h2 class="text-lg font-bold text-gray-900">My Orders</h2>
                <p class="text-xs text-gray-400 mt-1">Riwayat registrasi milik participant pada akun yang sedang aktif.
                </p>
            </div>
            <span class="badge badge-info badge-sm">{{ $this->myOrders->count() }} Orders</span>
        </div>

        @if ($this->myOrders->isEmpty())
        <div class="text-sm text-gray-500 border border-dashed border-gray-300 rounded-lg p-6 text-center">
            Belum ada order tersimpan untuk akun ini.
        </div>
        @else
        <div class="hidden md:block overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Participant</th>
                        <th>Items</th>
                        <th>Payment</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->myOrders as $order)
                    <tr>
                        <td>
                            <div class="font-bold text-gray-900">{{ $order->order_number }}</div>
                            <div class="text-xs text-gray-400">{{ $order->created_at?->format('d M Y H:i') }}</div>
                        </td>
                        <td>
                            <div class="font-semibold text-gray-900">{{ trim(($order->participant?->firstname ?? '') . '
                                ' . ($order->participant?->lastname ?? '')) ?: '-' }}</div>
                            <div class="text-xs text-gray-400">{{ $order->participant?->email }}</div>
                        </td>
                        <td>
                            <div class="space-y-1">
                                @foreach ($order->items as $item)
                                <div class="text-xs font-semibold">
                                    {{ $item->product?->name ?? 'Product deleted' }} x{{ $item->quantity }} <br>
                                    <span class="font-normal text-gray-700">
                                        {{ $item->product?->categoryProduct?->name ?: 'Registration' }}
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            <div class="font-semibold text-gray-900">{{ $order->payment?->payment_method ===
                                'bank_transfer' ? 'Bank Transfer' : 'Online Payment' }}</div>
                            <div class="text-xs text-gray-400">{{ $order->payment?->transaction_id }}</div>
                        </td>
                        <td class="font-bold text-gray-900">{{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td>
                            <span
                                class="badge {{ $order->payment_status === 'paid' ? 'badge-success' : 'badge-warning' }} badge-sm">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-1 gap-4 md:hidden">
            @foreach ($this->myOrders as $order)
            <div class="border border-gray-100 rounded-lg p-4">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="font-bold text-gray-900">{{ $order->order_number }}</p>
                        <p class="text-xs text-gray-400">{{ $order->created_at?->format('d M Y H:i') }}</p>
                    </div>
                    <span
                        class="badge {{ $order->payment_status === 'paid' ? 'badge-success' : 'badge-warning' }} badge-sm">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
                <div class="mt-3 text-sm flex items-center justify-between gap-3">
                    {{ $order->items->first()?->product?->name ?? 'Product deleted' }} x{{ $order->items->first()?->quantity }} <br>
                    <p class="font-normal">
                        {{ $order->items->first()?->product?->categoryProduct?->name ?: 'Registration' }}
                    </p>
                </div>
                <div class="mt-3 text-sm text-gray-700 space-y-1">
                    <p><span class="text-gray-400">Participant:</span> {{ trim(($order->participant?->firstname ?? '') .
                        ' ' . ($order->participant?->lastname ?? '')) ?: '-' }}</p>
                    <p><span class="text-gray-400">Payment:</span> {{ $order->payment?->payment_method ===
                        'bank_transfer' ? 'Bank Transfer' : 'Online Payment' }}</p>
                    <p><span class="text-gray-400">Total:</span> {{ number_format($order->total_amount, 0, ',', '.') }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    {{--
    <livewire:registration.create :product="request()->query('product')" /> --}}
</x-filament-panels::page>