<?php

namespace Lyre\Commerce\Filament\Resources\ProductResource\RelationManagers;

use Lyre\Commerce\Filament\Resources\ProductVariantResource;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ProductVariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    public function form(Schema $form): Schema
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\Toggle::make('enabled')->default(true),
            Forms\Components\KeyValue::make('attributes')->columnSpanFull(),
            Forms\Components\TextInput::make('barcode'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('enabled')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make(),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\DeleteBulkAction::make(),
            ])
            ->striped()
            ->deferLoading()
            ->defaultSort('created_at', 'desc');
    }
}

