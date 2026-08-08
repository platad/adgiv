<?php

namespace App\Filament\Widgets;

use App\Models\TranscriptLineFeedback;
use Filament\Widgets\BarChartWidget;

class LineFeedbackChartWidget extends BarChartWidget
{
    protected static ?int $sort = 4;

    protected string $color = 'warning';

    protected ?string $heading = 'Evaluasi Kalimat (Like / Dislike)';

    protected ?string $description = 'Jumlah total 👍 Like dan 👎 Dislike dari semua sesi analisa';

    protected function getData(): array
    {
        $likes    = TranscriptLineFeedback::where('feedback', 'like')->count();
        $dislikes = TranscriptLineFeedback::where('feedback', 'dislike')->count();

        return [
            'datasets' => [
                [
                    'label'           => '👍 Suka (Like)',
                    'data'            => [$likes],
                    'backgroundColor' => '#22c55e',
                    'borderColor'     => '#16a34a',
                    'borderWidth'     => 2,
                    'borderRadius'    => 6,
                ],
                [
                    'label'           => '👎 Tidak Suka (Dislike)',
                    'data'            => [$dislikes],
                    'backgroundColor' => '#ef4444',
                    'borderColor'     => '#dc2626',
                    'borderWidth'     => 2,
                    'borderRadius'    => 6,
                ],
            ],
            'labels' => ['Evaluasi Kalimat'],
        ];
    }
}
