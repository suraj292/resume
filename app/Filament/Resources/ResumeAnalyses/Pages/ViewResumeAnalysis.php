<?php

namespace App\Filament\Resources\ResumeAnalyses\Pages;

use App\Filament\Resources\ResumeAnalyses\ResumeAnalysisResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\KeyValue;
use Filament\Schemas\Components\Repeater;
use Filament\Schemas\Components\Split;
use Filament\Schemas\Components\Textarea;
use Filament\Schemas\Components\TextEntry;
use Filament\Schemas\Schema;

class ViewResumeAnalysis extends ViewRecord
{
    protected static string $resource = ResumeAnalysisResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}

