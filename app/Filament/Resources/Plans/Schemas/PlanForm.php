<?php

namespace App\Filament\Resources\Plans\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Basic Information
                TextInput::make('name')
                    ->required()
                    ->label('Plan Name'),
                TextInput::make('slug')
                    ->required()
                    ->label('Slug'),
                Textarea::make('description')
                    ->rows(2)
                    ->columnSpanFull(),
                
                // Pricing
                TextInput::make('currency')
                    ->required()
                    ->default('$')
                    ->label('Currency Symbol'),
                TextInput::make('currency_code')
                    ->required()
                    ->default('USD')
                    ->label('Currency Code (USD/INR)'),
                TextInput::make('monthly_price')
                    ->numeric()
                    ->default(0)
                    ->prefix('$')
                    ->label('Monthly Price'),
                TextInput::make('yearly_price')
                    ->numeric()
                    ->default(0)
                    ->prefix('$')
                    ->label('Yearly Price'),
                
                // Usage Limits
                TextInput::make('resume_limit')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->helperText('Set -1 for unlimited resumes')
                    ->label('Resume Limit'),
                TextInput::make('ats_scan_limit')
                    ->required()
                    ->numeric()
                    ->default(3)
                    ->helperText('Set -1 for unlimited ATS scans')
                    ->label('ATS Scan Limit'),
                
                // Features
                Toggle::make('ai_optimization')
                    ->label('AI Resume Optimization')
                    ->default(false),
                Toggle::make('cover_letter')
                    ->label('Cover Letter Generator')
                    ->default(false),
                Toggle::make('is_popular')
                    ->label('Mark as Popular')
                    ->default(false),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->label('Sort Order'),
                
                // Template Access
                CheckboxList::make('templates')
                    ->relationship('templates', 'name')
                    ->label('Available Templates')
                    ->helperText('Select which templates users with this plan can access')
                    ->columns(3)
                    ->searchable()
                    ->bulkToggleable()
                    ->columnSpanFull(),
            ]);
    }
}
