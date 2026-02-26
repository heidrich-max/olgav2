<?php

namespace App\Providers;

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
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                $view->with('openTodoCount', \App\Models\Todo::where('user_id', \Illuminate\Support\Facades\Auth::id())
                    ->where('is_completed', false)
                    ->count());
            }
        });
    }
}
