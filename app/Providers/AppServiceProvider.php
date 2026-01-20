<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Expense;
use App\Observers\ExpenseObserver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;


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
        Expense::observe(ExpenseObserver::class);
        Blade::component('chatbot-widget', \App\View\Components\ChatbotWidget::class);
        if (app()->environment('local')) {
        URL::forceScheme('https');
    }
    }
}
