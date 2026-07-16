<?php

namespace App\Filament\Resources\Product\CategoryProducts;

use App\Filament\Resources\Product\CategoryProducts\Pages\CreateCategoryProduct;
use App\Filament\Resources\Product\CategoryProducts\Pages\EditCategoryProduct;
use App\Filament\Resources\Product\CategoryProducts\Pages\ListCategoryProducts;
use App\Filament\Resources\Product\CategoryProducts\Pages\ManageCategoryProductProducts;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MarkdownEditor;
use App\Models\Product\CategoryProduct;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoryProductResource extends Resource
{
    protected static ?string $model = CategoryProduct::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Square3Stack3d;
    protected static string|UnitEnum|null $navigationGroup = 'Manage';
    protected static ?string $navigationLabel = 'Products Registration';
    protected static ?string $recordTitleAttribute = 'Product & Category Product';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('date')
                    ->required()
                    ->native(false),
                MarkdownEditor::make('description')
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('date')
                    ->date('d M Y'),
                TextEntry::make('description'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('description')
                    ->searchable(),
                TextColumn::make('date')
                    ->sortable()
                    ->date('d M Y'),
                TextColumn::make('products_count')
                    ->label('Total Products')
                    ->counts('products')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('products')
                    ->label('Products')
                    ->icon('heroicon-o-rectangle-stack')
                    ->url(fn (CategoryProduct $record): string => static::getUrl('products', ['record' => $record])),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategoryProducts::route('/'),
            'create' => CreateCategoryProduct::route('/create'),
            'edit' => EditCategoryProduct::route('/{record}/edit'),
            'products' => ManageCategoryProductProducts::route('/{record}/products'),
        ];
    }
}
