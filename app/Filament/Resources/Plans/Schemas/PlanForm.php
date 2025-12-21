<?php

namespace App\Filament\Resources\Plans\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Textarea::make('description')
                    ->maxLength(65535),
                Select::make('currency')
                    ->options([
                        'INR' => 'Indian Rupee (INR)',
                        'USD' => 'US Dollar (USD)',
                        'EUR' => 'Euro (EUR)',
                    ])
                    ->default('INR')
                    ->required(),
                TextInput::make('monthly_price')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->prefix('₹'),
                TextInput::make('yearly_price')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->prefix('₹'),
                KeyValue::make('features')
                    ->label('Features')
                    ->keyLabel('Feature')
                    ->valueLabel('Description')
                    ->addActionLabel('Add Feature'),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
                Checkbox::make('is_popular')
                    ->label('Mark as Popular Plan'),
                Checkbox::make('is_active')
                    ->label('Active Plan')
                    ->default(true),
            ]);
    }
}
