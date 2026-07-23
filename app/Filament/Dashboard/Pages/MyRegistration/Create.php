<?php

namespace App\Filament\Dashboard\Pages\MyRegistration;

use BackedEnum;
use Filament\Pages\Dashboard;
use Filament\Support\Icons\Heroicon;
use Filament\Pages\Page;



class Create extends Page
{
    protected  string $view = 'filament.dashboard.pages.my-registration.create';
    protected static ?string $slug = 'registration/create';

    protected static ?string $title = 'Add Registration';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Plus;

    protected static bool $shouldRegisterNavigation = false;

    public function getBreadcrumbs(): array
    {
        return [
            Dashboard::getUrl(panel: 'dashboard') => 'Dashboard',
            Index::getUrl(panel: 'dashboard') => 'Registration',
            static::getUrl(panel: 'dashboard') => 'Create',
        ];
    }
    

    
}
