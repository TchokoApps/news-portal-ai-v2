<?php

namespace App\Providers;

use App\Models\Language;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('frontend.layouts.header', function ($view): void {
            $frontendLanguages = Language::query()
                ->where('status', true)
                ->orderByDesc('default')
                ->orderBy('name')
                ->get();

            $view->with([
                'frontendLanguages' => $frontendLanguages,
                'currentFrontendLanguageCode' => current_language(),
            ]);
        });
    }
}
