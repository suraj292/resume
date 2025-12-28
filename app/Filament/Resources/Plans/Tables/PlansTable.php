<?php

namespace App\Filament\Resources\Plans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('monthly_price')
                    ->money(fn ($record) => $record->currency_code ?? 'USD')
                    ->sortable(),
                TextColumn::make('yearly_price')
                    ->money(fn ($record) => $record->currency_code ?? 'USD')
                    ->sortable(),
                TextColumn::make('monthly_price_usd')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('yearly_price_usd')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('monthly_price_inr')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('yearly_price_inr')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_popular')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('resume_limit')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('ats_scan_limit')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('ai_optimization')
                    ->boolean(),
                IconColumn::make('cover_letter')
                    ->boolean(),
                TextColumn::make('currency')
                    ->searchable(),
                TextColumn::make('currency_code')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
