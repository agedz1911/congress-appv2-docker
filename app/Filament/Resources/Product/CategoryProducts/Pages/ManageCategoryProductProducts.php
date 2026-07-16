<?php

namespace App\Filament\Resources\Product\CategoryProducts\Pages;

use App\Filament\Resources\Product\CategoryProducts\CategoryProductResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ManageCategoryProductProducts extends ManageRelatedRecords
{
    protected static string $resource = CategoryProductResource::class;

    protected static string $relationship = 'products';

    protected static ?string $title = 'Products';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('type_product')
                    ->options([
                        'Early Bird Registration' => 'Early Bird Registration',
                        'Normal Registration' => 'Normal Registration',
                        'Onsite Registration' => 'Onsite Registration',
                    ])
                    ->required(),
                Select::make('type_specialization')
                    ->options([
                        'Specialist' => 'Specialist',
                        'Resident' => 'Resident',
                        'General Practitioner' => 'General Practitioner',
                        'Medical Student' => 'Medical Student',
                    ])
                    ->required(),
                TextInput::make('price')
                    ->numeric()
                    ->required(),
                TextInput::make('stock')
                    ->numeric()
                    ->required(),
                DatePicker::make('date_start')
                    ->required()
                    ->beforeOrEqual('date_end')
                    ->native(false),
                DatePicker::make('date_end')
                    ->required()
                    ->afterOrEqual('date_start')
                    ->native(false),
                MarkdownEditor::make('description')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('type_product')
                    ->label('Type Product')
                    ->searchable(),
                TextColumn::make('type_specialization')
                    ->label('Type Specialization')
                    ->searchable(),
                TextColumn::make('price')
                    ->sortable(),
                TextColumn::make('stock')
                    ->sortable(),
                TextColumn::make('date_start')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('date_end')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
