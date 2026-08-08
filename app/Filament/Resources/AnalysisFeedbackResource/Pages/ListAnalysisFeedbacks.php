<?php

namespace App\Filament\Resources\AnalysisFeedbackResource\Pages;

use App\Filament\Resources\AnalysisFeedbackResource;
use Filament\Resources\Pages\ListRecords;

class ListAnalysisFeedbacks extends ListRecords
{
    protected static string $resource = AnalysisFeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
