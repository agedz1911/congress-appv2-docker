<?php

namespace App\Filament\Dashboard\Pages\MyRegistration;

use App\Models\Transaction\Order;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class Index extends Page
{
    protected string $view = 'filament.dashboard.pages.my-registration.index';

    protected static ?string $navigationLabel = 'My Registration';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ShoppingBag;
    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'My Registration';

    protected static ?string $slug = 'registration';

    public function getMyOrdersProperty(): Collection
    {
        $userId = Auth::id();

        if (! $userId) {
            return collect();
        }

        return Order::query()
            ->with([
                'participant',
                'payment',
                'items.product',
            ])
            ->where(function ($query) use ($userId) {
                $query
                    ->where('user_id', $userId)
                    ->orWhereHas('participant', fn ($participantQuery) => $participantQuery->where('user_id', $userId));
            })
            ->latest()
            ->get();
    }
}
