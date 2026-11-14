<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class LaravelBackupPanelServiceProvider extends ServiceProvider
{
    /**
     * Register the Laravel Backup Panel gate.
     *
     * This gate determines who can access Laravel Backup Panel in non-local environments.
     *
     * @return void
     */
    public function boot(): void
    {
        Gate::define('viewLaravelBackupPanel', function ($user) {
            return in_array($user->email, [
                'a.parwiz.n@gmail.com'
                
            ]);
        });
    }
}
