<?php

use Illuminate\Support\Facades\Route;

Route::get('/debug-openai', function () {
    return [
        'env_key' => env('OPENAI_API_KEY') ? 'VORHANDEN (Erste 10 Zeichen: ' . substr(env('OPENAI_API_KEY'), 0, 10) . '...)' : 'NICHT GEFUNDEN',
        'config_key' => config('services.openai.key') ? 'VORHANDEN (Erste 10 Zeichen: ' . substr(config('services.openai.key'), 0, 10) . '...)' : 'NICHT GEFUNDEN',
        'all_services' => config('services.openai'),
    ];
});
