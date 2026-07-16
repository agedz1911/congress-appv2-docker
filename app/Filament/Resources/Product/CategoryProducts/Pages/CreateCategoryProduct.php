<?php

namespace App\Filament\Resources\Product\CategoryProducts\Pages;

use App\Filament\Resources\Product\CategoryProducts\CategoryProductResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategoryProduct extends CreateRecord
{
    protected static string $resource = CategoryProductResource::class;
}
