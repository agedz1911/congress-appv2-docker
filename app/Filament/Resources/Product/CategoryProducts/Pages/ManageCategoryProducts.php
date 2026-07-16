<?php

namespace App\Filament\Resources\Product\CategoryProducts\Pages;

use App\Filament\Resources\Product\CategoryProducts\CategoryProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCategoryProducts extends ManageRecords
{
    protected static string $resource = CategoryProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
