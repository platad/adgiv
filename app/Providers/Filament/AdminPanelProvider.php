<?php

namespace App\Providers\Filament;

use App\Filament\Resources\AnalysisFeedbackResource;
use App\Filament\Resources\AnalysisResource;
use App\Filament\Resources\UserResource;
use App\Filament\Widgets\AccuracyRatingChartWidget;
use App\Filament\Widgets\AnalysisChartWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('BIMA Admin')
            ->colors([
                'primary' => Color::Red,
                'gray'    => Color::Slate,
            ])
            ->resources([
                UserResource::class,
                AnalysisResource::class,
                AnalysisFeedbackResource::class,
            ])
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                StatsOverviewWidget::class,
                AnalysisChartWidget::class,
                AccuracyRatingChartWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
