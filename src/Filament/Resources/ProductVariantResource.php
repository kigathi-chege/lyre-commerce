<?php

namespace Lyre\Commerce\Filament\Resources;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Lyre\Commerce\Filament\Resources\ProductVariantResource\Pages;
use Lyre\Commerce\Models\ProductVariant;
use UnitEnum;

class ProductVariantResource extends Resource
{
    protected static ?string $model = ProductVariant::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cube';

    public static function getNavigationGroup(): string | UnitEnum | null
    {
        return 'Commerce';
    }

    protected static ?int $navigationSort = 4;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Forms\Components\Select::make('product_id')
                ->relationship('product', 'name')
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\Toggle::make('enabled')->default(true),
            Forms\Components\KeyValue::make('attributes')->columnSpanFull(),
            Forms\Components\TextInput::make('barcode'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('product.name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\IconColumn::make('enabled')->boolean(),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
        ])
            ->filters([])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductVariants::route('/'),
            'create' => Pages\CreateProductVariant::route('/create'),
            'edit' => Pages\EditProductVariant::route('/{record}/edit'),
        ];
    }
}
