<?php

use App\Models\Product\CategoryProduct;
use Livewire\Component;

new class extends Component
{
    public $catProducts = [];

    public function mount()
    {
        $today = today()->toDateString();

        $this->catProducts = CategoryProduct::query()
            ->whereHas('products', function ($query) use ($today) {
                $query
                    ->whereDate('date_start', '<=', $today)
                    ->whereDate('date_end', '>=', $today);
            })
            ->with(['products' => function ($query) use ($today) {
                $query
                    ->whereDate('date_start', '<=', $today)
                    ->whereDate('date_end', '>=', $today)
                    ->orderBy('date_start', 'asc');
            }])
            ->orderBy('name', 'asc')
            ->get();
    }
};
