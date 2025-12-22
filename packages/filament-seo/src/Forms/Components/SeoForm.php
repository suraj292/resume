<?php

namespace Webvio\FilamentSeo\Forms\Components;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;

class SeoForm
{
    /**
     * Get the complete SEO form section.
     */
    public static function make(): Section
    {
        return Section::make('SEO Settings')
            ->schema([
                Tabs::make('SEO Tabs')
                    ->tabs([
                        Tab::make('Basic Meta')
                            ->schema(self::basicMetaFields()),
                        
                        Tab::make('Open Graph')
                            ->schema(self::openGraphFields()),
                        
                        Tab::make('Twitter Card')
                            ->schema(self::twitterCardFields()),
                        
                        Tab::make('Technical SEO')
                            ->schema(self::technicalSeoFields()),
                        
                        Tab::make('Schema Markup')
                            ->schema(self::schemaMarkupFields()),
                    ])
                    ->columnSpanFull(),
            ])
            ->collapsible()
            ->collapsed(false);
    }

    /**
     * Basic meta fields.
     */
    protected static function basicMetaFields(): array
    {
        return [
            TextInput::make('seo.meta_title')
                ->label('Meta Title')
                ->placeholder('Leave empty to use default title')
                ->maxLength(60)
                ->helperText('Recommended: 50-60 characters')
                ->columnSpanFull(),

            Textarea::make('seo.meta_description')
                ->label('Meta Description')
                ->placeholder('Leave empty to use default description')
                ->maxLength(160)
                ->rows(3)
                ->helperText('Recommended: 150-160 characters')
                ->columnSpanFull(),

            Textarea::make('seo.meta_keywords')
                ->label('Meta Keywords')
                ->placeholder('keyword1, keyword2, keyword3')
                ->maxLength(255)
                ->rows(2)
                ->helperText('Comma-separated keywords (optional)')
                ->columnSpanFull(),
        ];
    }

    /**
     * Open Graph fields for social sharing.
     */
    protected static function openGraphFields(): array
    {
        return [
            TextInput::make('seo.og_title')
                ->label('OG Title')
                ->placeholder('Leave empty to use meta title')
                ->maxLength(95)
                ->helperText('Recommended: 60-95 characters')
                ->columnSpanFull(),

            Textarea::make('seo.og_description')
                ->label('OG Description')
                ->placeholder('Leave empty to use meta description')
                ->maxLength(200)
                ->rows(3)
                ->helperText('Recommended: 110-200 characters')
                ->columnSpanFull(),

            FileUpload::make('seo.og_image')
                ->label('OG Image')
                ->image()
                ->disk(config('filament-seo.storage_disk', 'public'))
                ->directory(config('filament-seo.og_images_directory', 'seo/og-images'))
                ->visibility('public')
                ->imageEditor()
                ->imageEditorAspectRatios(['1.91:1'])
                ->maxSize(5120)
                ->helperText('Recommended: 1200x630px (1.91:1 ratio)')
                ->columnSpanFull(),

            TextInput::make('seo.og_type')
                ->label('OG Type')
                ->default('website')
                ->placeholder('website, article, product, etc.')
                ->maxLength(50)
                ->columnSpanFull(),
        ];
    }

    /**
     * Twitter Card fields.
     */
    protected static function twitterCardFields(): array
    {
        return [
            TextInput::make('seo.twitter_card')
                ->label('Twitter Card Type')
                ->default('summary_large_image')
                ->placeholder('summary, summary_large_image')
                ->maxLength(50)
                ->helperText('Type of Twitter card')
                ->columnSpanFull(),

            TextInput::make('seo.twitter_title')
                ->label('Twitter Title')
                ->placeholder('Leave empty to use OG title')
                ->maxLength(70)
                ->helperText('Recommended: max 70 characters')
                ->columnSpanFull(),

            Textarea::make('seo.twitter_description')
                ->label('Twitter Description')
                ->placeholder('Leave empty to use OG description')
                ->maxLength(200)
                ->rows(3)
                ->helperText('Recommended: max 200 characters')
                ->columnSpanFull(),

            FileUpload::make('seo.twitter_image')
                ->label('Twitter Image')
                ->image()
                ->disk(config('filament-seo.storage_disk', 'public'))
                ->directory(config('filament-seo.twitter_images_directory', 'seo/twitter-images'))
                ->visibility('public')
                ->imageEditor()
                ->imageEditorAspectRatios(['2:1', '1:1'])
                ->maxSize(5120)
                ->helperText('summary_large_image: 800x418px | summary: 120x120px')
                ->columnSpanFull(),

            TextInput::make('seo.twitter_site')
                ->label('Twitter Site')
                ->placeholder('@yourusername')
                ->maxLength(50)
                ->helperText('Twitter username of website (e.g., @yoursite)')
                ->columnSpanFull(),

            TextInput::make('seo.twitter_creator')
                ->label('Twitter Creator')
                ->placeholder('@creatorusername')
                ->maxLength(50)
                ->helperText('Twitter username of content creator')
                ->columnSpanFull(),
        ];
    }

    /**
     * Technical SEO fields.
     */
    protected static function technicalSeoFields(): array
    {
        return [
            TextInput::make('seo.canonical_url')
                ->label('Canonical URL')
                ->url()
                ->placeholder('https://example.com/page')
                ->helperText('Leave empty to auto-generate from current URL')
                ->columnSpanFull(),

            Toggle::make('seo.index')
                ->label('Allow Search Engine Indexing')
                ->default(true)
                ->helperText('Allow search engines to index this page')
                ->inline(false)
                ->columnSpanFull(),

            Toggle::make('seo.follow')
                ->label('Allow Following Links')
                ->default(true)
                ->helperText('Allow search engines to follow links on this page')
                ->inline(false)
                ->columnSpanFull(),

            TextInput::make('seo.locale')
                ->label('Locale')
                ->default(config('filament-seo.default_locale', 'en_US'))
                ->placeholder('en_US, fr_FR, es_ES')
                ->maxLength(10)
                ->helperText('Language and region code (e.g., en_US)')
                ->columnSpanFull(),

            TextInput::make('seo.priority')
                ->label('Sitemap Priority')
                ->numeric()
                ->minValue(0.0)
                ->maxValue(1.0)
                ->step(0.1)
                ->default(0.5)
                ->helperText('Sitemap priority (0.0 - 1.0, default: 0.5)')
                ->columnSpanFull(),
        ];
    }

    /**
     * Schema markup fields.
     */
    protected static function schemaMarkupFields(): array
    {
        return [
            KeyValue::make('seo.schema_markup')
                ->label('Schema Markup (JSON-LD)')
                ->keyLabel('Property')
                ->valueLabel('Value')
                ->addActionLabel('Add Schema Property')
                ->helperText('Add custom schema.org JSON-LD properties')
                ->columnSpanFull(),

            Textarea::make('seo_schema_preview')
                ->label('Schema JSON Preview')
                ->placeholder('Schema markup will appear here...')
                ->rows(8)
                ->disabled()
                ->helperText('Preview of generated schema markup (read-only)')
                ->columnSpanFull()
                ->formatStateUsing(function ($state, $record) {
                    if (!$record || !$record->seo || !$record->seo->schema_markup) {
                        return null;
                    }
                    
                    return json_encode($record->seo->schema_markup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                }),
        ];
    }

    /**
     * Get minimal SEO form (for simple resources).
     */
    public static function minimal(): Section
    {
        return Section::make('SEO Settings')
            ->schema([
                TextInput::make('seo.meta_title')
                    ->label('Meta Title')
                    ->placeholder('Leave empty to use default title')
                    ->maxLength(60)
                    ->columnSpanFull(),

                Textarea::make('seo.meta_description')
                    ->label('Meta Description')
                    ->placeholder('Leave empty to use default description')
                    ->maxLength(160)
                    ->rows(3)
                    ->columnSpanFull(),

                FileUpload::make('seo.og_image')
                    ->label('Social Share Image')
                    ->image()
                    ->disk(config('filament-seo.storage_disk', 'public'))
                    ->directory(config('filament-seo.og_images_directory', 'seo/og-images'))
                    ->visibility('public')
                    ->columnSpanFull(),

                Toggle::make('seo.index')
                    ->label('Allow Search Engine Indexing')
                    ->default(true)
                    ->inline(false)
                    ->columnSpanFull(),
            ])
            ->collapsible()
            ->collapsed(true);
    }
}
