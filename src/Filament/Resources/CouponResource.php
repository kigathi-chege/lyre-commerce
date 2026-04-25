<?php

namespace Lyre\Commerce\Filament\Resources;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Lyre\Commerce\Filament\Resources\CouponResource\Pages;
use Lyre\Commerce\Filament\Resources\CouponResource\RelationManagers;
use Lyre\Commerce\Models\Coupon;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-ticket';
    public static function getNavigationGroup(): ?string
    {
        return 'Commerce';
    }

    protected static ?int $navigationSort = 11;

    public static function form(Schema $form): Schema
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('discount')->numeric()->required(),
            Forms\Components\Select::make('discount_type')
                ->options(['percent' => 'Percent', 'fixed' => 'Fixed'])
                ->required()
                ->default('percent'),
            Forms\Components\DateTimePicker::make('start_date'),
            Forms\Components\DateTimePicker::make('end_date'),
            Forms\Components\Select::make('status')
                ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                ->default('active'),
            Forms\Components\TextInput::make('usage_limit')->numeric(),
            Forms\Components\TextInput::make('minimum_amount')->numeric(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('code')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('discount')->numeric()->sortable(),
            Tables\Columns\TextColumn::make('discount_type')->badge(),
            Tables\Columns\TextColumn::make('status')->badge()
                ->colors(['success' => 'active', 'danger' => 'inactive']),
            Tables\Columns\TextColumn::make('used_count')->numeric()->sortable(),
            Tables\Columns\TextColumn::make('usage_limit')->numeric(),
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
            RelationManagers\CouponUsagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}

