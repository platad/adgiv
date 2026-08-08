<?php

namespace App\Filament\Widgets;

use App\Models\Analysis;
use App\Models\AnalysisFeedback;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalAnalyses    = Analysis::count();
        $completedAnalyses = Analysis::where('status', 'completed')->count();
        $weeklyAnalyses   = Analysis::where('created_at', '>=', now()->subWeek())->where('status', 'completed')->count();
        $totalUsers       = User::count();

        $feedbackCount    = AnalysisFeedback::count();
        $accurateCount    = AnalysisFeedback::where('is_accurate', true)->count();
        $accuracyRate     = $feedbackCount > 0
            ? round(($accurateCount / $feedbackCount) * 100, 1)
            : 0;



        $trend = Analysis::where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count')
            ->toArray();

        return [
            Stat::make('Total Laporan Generated', $totalAnalyses)
                ->description($completedAnalyses . ' laporan selesai')
                ->descriptionIcon('heroicon-m-document-chart-bar')
                ->chart($trend)
                ->color('primary'),

            Stat::make('Laporan Minggu Ini', $weeklyAnalyses)
                ->description('Analisa selesai 7 hari terakhir')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Pengguna', $totalUsers)
                ->description(User::where('is_admin', true)->count() . ' admin aktif')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Rata-rata Akurasi', $accuracyRate . '%')
                ->description($feedbackCount . ' penilaian dari pengguna')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color($accuracyRate >= 70 ? 'success' : 'warning'),

        ];
    }
}
