<?php

namespace App\Filament\Resources\Blogs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BlogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')->required(),
                                TextInput::make('slug')->required(),
                            ]),
                    ]),

                Section::make('Content')
                    ->schema([
                        Textarea::make('excerpt')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        RichEditor::make('content')
                            ->required()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'h2',
                                'h3',
                                'blockquote',
                                'codeBlock',
                                'bulletList',
                                'orderedList',
                                'redo',
                                'undo',
                            ])
                            ->columnSpanFull(),
                        TextInput::make('featured_image')
                            ->label('Featured Image URL')
                            ->url()
                            ->maxLength(255),
                    ]),

                Section::make('Metadata')
                    ->schema([
                        TextInput::make('author')
                            ->maxLength(255),
                        TextInput::make('read_time')
                            ->required()
                            ->numeric()
                            ->default(5)
                            ->suffix('minutes'),
                        TextInput::make('views_count')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false),
                    ]),

                Section::make('Publishing')
                    ->schema([
                        Toggle::make('is_featured')
                            ->label('Featured Post')
                            ->default(false),
                        Toggle::make('is_published')
                            ->label('Published')
                            ->default(false)
                            ->live(),
                        DateTimePicker::make('published_at')
                            ->label('Publish Date')
                            ->default(now())
                            ->required(fn ($get) => $get('is_published')),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ]),


            ]);
    }
}
