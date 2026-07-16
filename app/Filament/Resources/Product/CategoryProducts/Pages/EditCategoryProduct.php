<?php

namespace App\Filament\Resources\Product\CategoryProducts\Pages;

use App\Filament\Resources\Product\CategoryProducts\CategoryProductResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCategoryProduct extends EditRecord
{
    protected static string $resource = CategoryProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('products')
                ->label('Manage Products')
                ->icon('heroicon-o-rectangle-stack')
                ->url(fn (): string => CategoryProductResource::getUrl('products', ['record' => $this->getRecord()])),
            DeleteAction::make(),
        ];
    }
}
