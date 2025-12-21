<?php

namespace App\Filament\Resources\ResumeAnalyses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ResumeAnalysesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->default('Guest'),

                BadgeColumn::make('input_type')
                    ->label('Type')
                    ->colors([
                        'primary' => 'upload',
                        'success' => 'paste',
                    ]),

                TextColumn::make('file_name')
                    ->label('File Name')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->file_name)
                    ->default('-'),

                TextColumn::make('ats_score')
                    ->label('ATS Score')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'warning',
                        $state >= 40 => 'danger',
                        default => 'danger'
                    })
                    ->formatStateUsing(fn ($state) => $state . '/100'),

                BadgeColumn::make('score_grade')
                    ->label('Grade')
                    ->colors([
                        'success' => 'Excellent',
                        'warning' => 'Good',
                        'danger' => fn ($state) => in_array($state, ['Needs Improvement', 'Poor']),
                    ]),

                TextColumn::make('experience_level')
                    ->label('Experience')
                    ->badge()
                    ->color('info'),

                TextColumn::make('word_count')
                    ->label('Words')
                    ->sortable()
                    ->numeric(),

                TextColumn::make('keyword_match_percentage')
                    ->label('Keyword Match')
                    ->sortable()
                    ->suffix('%')
                    ->color(fn ($state) => $state >= 70 ? 'success' : ($state >= 50 ? 'warning' : 'danger')),

                TextColumn::make('created_at')
                    ->label('Analyzed At')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('input_type')
                    ->options([
                        'upload' => 'Upload',
                        'paste' => 'Paste',
                    ]),

                SelectFilter::make('experience_level')
                    ->options([
                        'Entry-Level' => 'Entry-Level',
                        'Mid-Level' => 'Mid-Level',
                        'Senior' => 'Senior',
                        'Executive' => 'Executive',
                    ]),

                Filter::make('ats_score')
                    ->form([
                        \Filament\Forms\Components\Select::make('score_range')
                            ->options([
                                '80-100' => 'Excellent (80-100)',
                                '60-79' => 'Good (60-79)',
                                '40-59' => 'Needs Improvement (40-59)',
                                '0-39' => 'Poor (0-39)',
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['score_range'],
                            function (Builder $query, $range) {
                                [$min, $max] = explode('-', $range);
                                return $query->whereBetween('ats_score', [(int)$min, (int)$max]);
                            }
                        );
                    }),

                Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('analyzed_from')
                            ->label('From Date'),
                        \Filament\Forms\Components\DatePicker::make('analyzed_until')
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['analyzed_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['analyzed_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

