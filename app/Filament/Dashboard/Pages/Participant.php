<?php

namespace App\Filament\Dashboard\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class Participant extends Page
{
    protected string $view = 'filament.dashboard.pages.participant';

    protected static ?string $navigationLabel = 'Participants';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::User;

    // protected static string | UnitEnum | null $navigationGroup = 'Election';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Participants';

    protected static ?string $slug = 'participants';

    public function getHeading(): string
    {
        return 'Participants';
    }

    public function getTitle(): string
    {
        return 'Participants';
    }
}
