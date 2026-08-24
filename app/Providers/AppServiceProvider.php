<?php

namespace App\Providers;

use App\Contracts\AI\LlmConfigurationInterface;
use App\Contracts\AI\MultiModalAnalysisInterface;
use App\Services\AI\BimaAnalysisConfiguration;
use App\Services\AI\OpenAiMultiModalService;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            MultiModalAnalysisInterface::class,
            OpenAiMultiModalService::class
        );

        $this->app->bind(
            LlmConfigurationInterface::class,
            BimaAnalysisConfiguration::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        if (env('APP_ENV') === 'production' || env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }
    }
}
