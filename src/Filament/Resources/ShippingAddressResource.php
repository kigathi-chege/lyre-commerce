<?php

namespace Lyre\Commerce\Filament\Resources;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Lyre\Commerce\Filament\Resources\ShippingAddressResource\Pages;
use Lyre\Commerce\Models\ShippingAddress;

class ShippingAddressResource extends Resource
{
    protected static ?string $model = ShippingAddress::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-home';
    public static function getNavigationGroup(): ?string
    {
        return 'Commerce';
    }

    protected static ?int $navigationSort = 2;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Forms\Components\TextInput::make('user_id')->required(),
            Forms\Components\TextInput::make('location_id'),
            Forms\Components\TextInput::make('delivery_method'),
            Forms\Components\TextInput::make('address_line_1'),
            Forms\Components\TextInput::make('city'),
            Forms\Components\TextInput::make('country'),
            Forms\Components\Toggle::make('is_default'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('user_id')->sortable(),
            Tables\Columns\TextColumn::make('delivery_method'),
            Tables\Columns\TextColumn::make('address_line_1')->limit(30),
            Tables\Columns\IconColumn::make('is_default')->boolean(),
        ])
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
            'index' => Pages\ListShippingAddresses::route('/'),
            'create' => Pages\CreateShippingAddress::route('/create'),
            'edit' => Pages\EditShippingAddress::route('/{record}/edit'),
        ];
    }
}


