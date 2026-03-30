<?php

namespace Workbench\App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Workbench\App\Models\User;

class WorkbenchServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Auto-login for testing UI (no real auth needed)
        Route::get('/login-test', function () {
            $user = User::firstOrCreate(
                ['email' => 'test@example.com'],
                ['name' => 'Test User', 'password' => bcrypt('password')]
            );

            auth()->login($user);

            return redirect()->route('artisan-runner.index');
        })->middleware('web')->name('login-test');

        // Fallback login route so auth middleware doesn't crash
        Route::get('/login', function () {
            return redirect()->route('login-test');
        })->middleware('web')->name('login');
    }
}
