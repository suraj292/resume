<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('currency')
                    ->required()
                    ->default('USD'),
                TextInput::make('plan_id')
                    ->numeric(),
                DateTimePicker::make('plan_started_at'),
                TextInput::make('resumes_created')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('ats_scans_used')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('provider'),
                TextInput::make('provider_id'),
                TextInput::make('avatar'),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password(),
                Textarea::make('two_factor_secret')
                    ->columnSpanFull(),
                Textarea::make('two_factor_recovery_codes')
                    ->columnSpanFull(),
                DateTimePicker::make('two_factor_confirmed_at'),
            ]);
    }
}
