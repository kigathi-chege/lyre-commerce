<?php

namespace Lyre\Commerce\Filament\Resources;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Lyre\Commerce\Filament\Resources\UserProductVariantResource\Pages;
use Lyre\Commerce\Filament\Resources\UserProductVariantResource\RelationManagers;
use Lyre\Commerce\Models\UserProductVariant;
use UnitEnum;

class UserProductVariantResource extends Resource
{
    protected static ?string $model = UserProductVariant::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-building-storefront';

    public static function getNavigationGroup(): string | UnitEnum | null
    {
        return 'Commerce';
    }

    protected static ?int $navigationSort = 5;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\Select::make('product_variant_id')
                ->relationship('productVariant', 'name')
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('sku')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('stock_level')->numeric()->default(0),
            Forms\Components\TextInput::make('min_qty')->numeric(),
            Forms\Components\TextInput::make('max_qty')->numeric(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('user.name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('productVariant.name')->searchable(),
            Tables\Columns\TextColumn::make('sku')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('stock_level')->numeric()->sortable(),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProductVariantPricesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserProductVariants::route('/'),
            'create' => Pages\CreateUserProductVariant::route('/create'),
            'edit' => Pages\EditUserProductVariant::route('/{record}/edit'),
        ];
    }
}
