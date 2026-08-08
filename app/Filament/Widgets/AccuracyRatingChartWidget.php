<?php

namespace App\Filament\Widgets;

use App\Models\AnalysisFeedback;
use Filament\Widgets\DoughnutChartWidget;

class AccuracyRatingChartWidget extends DoughnutChartWidget
{
    protected static ?int $sort = 3;

    protected string $color = 'success';

    protected ?string $heading = 'Penilaian Kesesuaian Hasil';

    protected ?string $description = 'Persentase penilaian akurat vs tidak akurat dari pengguna';

    protected function getData(): array
    {
        $accurate    = AnalysisFeedback::where('is_accurate', true)->count();
        $inaccurate  = AnalysisFeedback::where('is_accurate', false)->count();
        $noFeedback  = \App\Models\Analysis::where('status', 'completed')
            ->doesntHave('feedback')
            ->count();

        return [
            'datasets' => [
                [
                    'data'            => [$accurate, $inaccurate, $noFeedback],
                    'backgroundColor' => ['#22c55e', '#ef4444', '#94a3b8'],
                    'hoverBackgroundColor' => ['#16a34a', '#dc2626', '#64748b'],
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                ],
            ],
            'labels' => ['✅ Akurat', '❌ Tidak Akurat', '— Belum Dinilai'],
        ];
    }
}
