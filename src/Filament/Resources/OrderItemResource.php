<?php

namespace Lyre\Commerce\Filament\Resources;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Lyre\Commerce\Filament\Resources\OrderItemResource\Pages;
use Lyre\Commerce\Models\OrderItem;
use UnitEnum;

class OrderItemResource extends Resource
{
    protected static ?string $model = OrderItem::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-list-bullet';

    public static function getNavigationGroup(): string | UnitEnum | null
    {
        return 'Commerce';
    }

    protected static ?int $navigationSort = 99;

    public static function shouldRegisterNavigation(): bool
    {
        return false; // Hidden from navigation, managed via OrderResource relation manager
    }

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Forms\Components\Select::make('order_id')
                ->relationship('order', 'reference')
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\Select::make('product_variant_id')
                ->relationship('productVariant', 'name')
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('unit_price')->numeric()->required(),
            Forms\Components\TextInput::make('quantity')->numeric()->required()->default(1),
            Forms\Components\TextInput::make('subtotal')->numeric()->required(),
            Forms\Components\TextInput::make('currency')->default('USD'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('order.reference')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('productVariant.name')->searchable(),
            Tables\Columns\TextColumn::make('quantity')->numeric()->sortable(),
            Tables\Columns\TextColumn::make('unit_price')->money('currency')->sortable(),
            Tables\Columns\TextColumn::make('subtotal')->money('currency')->sortable(),
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
            'index' => Pages\ListOrderItems::route('/'),
            'create' => Pages\CreateOrderItem::route('/create'),
            'edit' => Pages\EditOrderItem::route('/{record}/edit'),
        ];
    }
}
