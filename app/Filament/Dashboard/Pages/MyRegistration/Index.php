<?php

namespace App\Filament\Dashboard\Pages\MyRegistration;

use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Pages\Page;

class Index extends Page
{
    protected string $view = 'filament.dashboard.pages.my-registration.index';

    protected static ?string $navigationLabel = 'My Registration';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::ShoppingBag;
    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'My Registration';

    protected static ?string $slug = 'registration';
}
