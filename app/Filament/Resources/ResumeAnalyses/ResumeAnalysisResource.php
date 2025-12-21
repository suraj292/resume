<?php

namespace App\Filament\Resources\ResumeAnalyses;

use App\Filament\Resources\ResumeAnalyses\Pages\ListResumeAnalyses;
use App\Filament\Resources\ResumeAnalyses\Pages\ViewResumeAnalysis;
use App\Filament\Resources\ResumeAnalyses\Schemas\ResumeAnalysisForm;
use App\Filament\Resources\ResumeAnalyses\Tables\ResumeAnalysesTable;
use App\Models\ResumeAnalysis;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ResumeAnalysisResource extends Resource
{
    protected static ?string $model = ResumeAnalysis::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Resume Analytics';

    protected static ?string $modelLabel = 'Resume Analysis';

    protected static ?string $pluralModelLabel = 'Resume Analyses';

    public static function form(Schema $schema): Schema
    {
        return ResumeAnalysisForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ResumeAnalysesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListResumeAnalyses::route('/'),
            'view' => ViewResumeAnalysis::route('/{record}'),
        ];
    }
}
