<div>
    <section class="w-full max-w-7xl mx-auto px-4 py-8">
        <div class="">
            @foreach ($catProducts as $catProduct)
            @if ($catProduct->products->isNotEmpty())
            <h4 class="text-xl font-bold mb-3 text-primary mt-5">{{ $catProduct->name }}</h4>
            @endif
            <div class="flex flex-wrap justify-start gap-4">
                @foreach ($catProduct->products as $product)
                <div class="card w-full md:w-96 bg-base-100 shadow-sm transition-shadow duration-300">
                    <div class="card-body">
                        <span class="badge badge-xs badge-info">{{$product->type_product}}</span>
                        <div class="flex justify-between">
                            <h2 class="text-3xl font-bold">{{ $product->name }}</h2>
                            <span class="text-xl">
                                @if ($product->is_discounted)
                                <span class="line-through text-error">{{ number_format($product->price, 0, ',', '.')
                                    }}</span>
                                <span class="text-success">{{ number_format($product->discounted_price, 0, ',', '.')
                                    }}</span>
                                @else
                                <span>{{ number_format($product->price, 0, ',', '.') }}</span>
                                @endif
                            </span>
                        </div>
                        {!! str($product->description)->markdown()->sanitizeHtml() !!}
                        <div class="mt-6">
                            @if (Route::has('filament.dashboard.auth.login'))
                            @auth
                            <a href="{{ \App\Filament\Dashboard\Pages\MyRegistration\Create::getUrl(['product' => $product->id], panel: 'dashboard') }}" class="btn btn-primary btn-block">Register Now!</a>
                            @else
                            <a href="{{ route('filament.dashboard.auth.login') }}" class="btn btn-primary btn-block">Log
                                in</a>
                            @endauth
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach

        </div>
    </section>
</div>