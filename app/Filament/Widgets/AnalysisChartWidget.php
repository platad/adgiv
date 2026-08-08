<?php

namespace App\Filament\Widgets;

use App\Models\Analysis;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Carbon;

class AnalysisChartWidget extends LineChartWidget
{
    protected static ?int $sort = 2;

    protected string $color = 'primary';

    protected ?string $heading = 'Laporan Dihasilkan (12 Bulan Terakhir)';

    protected ?string $description = 'Jumlah analisa yang selesai per bulan';

    protected function getData(): array
    {
        $months = collect(range(11, 0))->map(function (int $monthsAgo) {
            $date = Carbon::now()->subMonths($monthsAgo);
            return [
                'label' => $date->translatedFormat('M Y'),
                'start' => $date->startOfMonth()->copy(),
                'end'   => $date->endOfMonth()->copy(),
            ];
        });

        $counts = $months->map(fn ($m) =>
            Analysis::where('status', 'completed')
                ->whereBetween('created_at', [$m['start'], $m['end']])
                ->count()
        );

        return [
            'datasets' => [
                [
                    'label'           => 'Laporan Selesai',
                    'data'            => $counts->values()->toArray(),
                    'borderColor'     => '#ef4444',
                    'backgroundColor' => 'rgba(239,68,68,0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                ],
            ],
            'labels' => $months->pluck('label')->toArray(),
        ];
    }
}
