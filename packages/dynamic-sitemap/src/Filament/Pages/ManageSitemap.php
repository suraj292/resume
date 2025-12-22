<?php

namespace Webvio\DynamicSitemap\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class ManageSitemap extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static string $view = 'filament.pages.manage-sitemap';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Sitemap';

    protected static ?int $navigationSort = 90;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'enabled' => config('dynamic-sitemap.enabled', true),
            'cache_enabled' => config('dynamic-sitemap.cache.enabled', true),
            'cache_ttl' => config('dynamic-sitemap.cache.ttl', 3600),
            'static_routes_enabled' => config('dynamic-sitemap.static_routes.enabled', true),
            'static_changefreq' => config('dynamic-sitemap.static_routes.changefreq', 'weekly'),
            'static_priority' => config('dynamic-sitemap.static_routes.priority', 0.5),
            'exclude_patterns' => config('dynamic-sitemap.static_routes.exclude_patterns', []),
            'sections' => config('dynamic-sitemap.sections', []),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General Settings')
                    ->schema([
                        Forms\Components\Toggle::make('enabled')
                            ->label('Enable Sitemap')
                            ->helperText('Enable or disable sitemap generation.')
                            ->default(true),

                        Forms\Components\Toggle::make('cache_enabled')
                            ->label('Enable Caching')
                            ->helperText('Cache generated sitemaps for better performance.')
                            ->default(true),

                        Forms\Components\TextInput::make('cache_ttl')
                            ->label('Cache TTL (seconds)')
                            ->helperText('How long to cache sitemap data (default: 3600 = 1 hour).')
                            ->numeric()
                            ->default(3600)
                            ->required(),
                    ]),

                Forms\Components\Section::make('Static Routes')
                    ->schema([
                        Forms\Components\Toggle::make('static_routes_enabled')
                            ->label('Enable Static Routes Sitemap')
                            ->helperText('Include static routes from routes/web.php in sitemap.')
                            ->default(true),

                        Forms\Components\Select::make('static_changefreq')
                            ->label('Change Frequency')
                            ->options([
                                'always' => 'Always',
                                'hourly' => 'Hourly',
                                'daily' => 'Daily',
                                'weekly' => 'Weekly',
                                'monthly' => 'Monthly',
                                'yearly' => 'Yearly',
                                'never' => 'Never',
                            ])
                            ->default('weekly')
                            ->required(),

                        Forms\Components\TextInput::make('static_priority')
                            ->label('Priority')
                            ->helperText('Default priority for static routes (0.0 to 1.0).')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1)
                            ->step(0.1)
                            ->default(0.5)
                            ->required(),

                        Forms\Components\KeyValue::make('exclude_patterns')
                            ->label('Exclude Patterns')
                            ->helperText('Route name patterns to exclude (e.g., filament.*, login, admin.*).')
                            ->keyLabel('Pattern')
                            ->valueLabel('Description')
                            ->default([
                                'filament.*' => 'Filament admin routes',
                                'login' => 'Login page',
                                'register' => 'Register page',
                            ]),
                    ]),

                Forms\Components\Section::make('Dynamic Sections')
                    ->schema([
                        Forms\Components\Repeater::make('sections')
                            ->label('Sitemap Sections')
                            ->schema([
                                Forms\Components\TextInput::make('key')
                                    ->label('Section Key')
                                    ->helperText('Unique identifier for this section.')
                                    ->required(),

                                Forms\Components\Toggle::make('enabled')
                                    ->label('Enabled')
                                    ->default(true),

                                Forms\Components\Select::make('type')
                                    ->label('Type')
                                    ->options([
                                        'model' => 'Model',
                                        'custom' => 'Custom',
                                    ])
                                    ->default('model')
                                    ->required(),

                                Forms\Components\TextInput::make('model')
                                    ->label('Model Class')
                                    ->helperText('e.g., App\\Models\\Company')
                                    ->visible(fn (Forms\Get $get) => $get('type') === 'model'),

                                Forms\Components\TextInput::make('route')
                                    ->label('Route Name')
                                    ->helperText('e.g., company.show')
                                    ->visible(fn (Forms\Get $get) => $get('type') === 'model'),

                                Forms\Components\TextInput::make('path')
                                    ->label('Sitemap Path')
                                    ->helperText('e.g., /sitemap-companies.xml')
                                    ->required(),

                                Forms\Components\Select::make('changefreq')
                                    ->label('Change Frequency')
                                    ->options([
                                        'always' => 'Always',
                                        'hourly' => 'Hourly',
                                        'daily' => 'Daily',
                                        'weekly' => 'Weekly',
                                        'monthly' => 'Monthly',
                                        'yearly' => 'Yearly',
                                        'never' => 'Never',
                                    ])
                                    ->default('daily')
                                    ->required(),

                                Forms\Components\TextInput::make('priority')
                                    ->label('Priority')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(1)
                                    ->step(0.1)
                                    ->default(0.8)
                                    ->required(),
                            ])
                            ->columns(2)
                            ->collapsible()
                            ->defaultItems(0),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Note: Changes are runtime only. To persist, edit config/dynamic-sitemap.php
        config([
            'dynamic-sitemap.enabled' => $data['enabled'],
            'dynamic-sitemap.cache.enabled' => $data['cache_enabled'],
            'dynamic-sitemap.cache.ttl' => $data['cache_ttl'],
            'dynamic-sitemap.static_routes.enabled' => $data['static_routes_enabled'],
            'dynamic-sitemap.static_routes.changefreq' => $data['static_changefreq'],
            'dynamic-sitemap.static_routes.priority' => $data['static_priority'],
            'dynamic-sitemap.static_routes.exclude_patterns' => $data['exclude_patterns'],
            'dynamic-sitemap.sections' => $data['sections'],
        ]);

        Notification::make()
            ->success()
            ->title('Settings updated')
            ->body('Runtime config updated. Edit config file to persist.')
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('save')
                ->label('Save Changes')
                ->submit('save'),
        ];
    }
}
