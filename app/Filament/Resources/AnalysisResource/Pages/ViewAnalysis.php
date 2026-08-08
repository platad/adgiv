<?php

namespace App\Filament\Resources\AnalysisResource\Pages;

use App\Filament\Resources\AnalysisResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewAnalysis extends ViewRecord
{
    protected static string $resource = AnalysisResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
