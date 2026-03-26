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
                $activeUser = \Illuminate\Support\Facades\Auth::user();
                $companyId = \Illuminate\Support\Facades\Session::get('active_company_id', \Illuminate\Support\Facades\Cookie::get('active_company_id', 1));
                if (!in_array($companyId, [1, 2])) { $companyId = 1; }

                $view->with([
                    'user' => $activeUser,
                    'companyId' => $companyId,
                    'companyName' => ($companyId == 1) ? 'Branding Europe GmbH' : 'Europe Pen GmbH',
                    'accentColor' => ($companyId == 1) ? '#1DA1F2' : '#0088CC',
                    'openTodoCount' => \App\Models\Todo::where('user_id', $activeUser->id)
                        ->where('is_completed', false)
                        ->count()
                ]);
            }
        });
    }
}
