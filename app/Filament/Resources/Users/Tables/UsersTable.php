<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('registration_method')
                    ->label('Registration Method')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Email' => 'success',
                        'Google' => 'info',
                        'LinkedIn' => 'warning',
                        'GitHub' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('social_accounts_count')
                    ->label('Social Accounts')
                    ->counts('socialAccounts')
                    ->badge(),
                TextColumn::make('email_verified_at')
                    ->label('Email Verified')
                    ->dateTime()
                    ->placeholder('Not verified')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Registered At')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('registration_method')
                    ->label('Registration Method')
                    ->options([
                        'Email' => 'Email',
                        'Google' => 'Google',
                        'LinkedIn' => 'LinkedIn',
                        'GitHub' => 'GitHub',
                    ]),
                SelectFilter::make('email_verified')
                    ->label('Email Verified')
                    ->options([
                        'verified' => 'Verified',
                        'unverified' => 'Unverified',
                    ])
                    ->query(function ($query, $data) {
                        if ($data['value'] === 'verified') {
                            $query->whereNotNull('email_verified_at');
                        } elseif ($data['value'] === 'unverified') {
                            $query->whereNull('email_verified_at');
                        }
                    }),
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
