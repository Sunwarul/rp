<?php

namespace NuxtIt\RP\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class RPServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/rp.php',
            'rp'
        );

        $this->registerCommands();

        Paginator::useBootstrapFive();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadRoutes();
        $this->loadViews();
        $this->publishConfig();
    }

    /**
     * Register package commands.
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \NuxtIt\RP\Console\Commands\SyncPermissionsCommand::class,
            ]);
        }
    }

    /**
     * Load package routes.
     */
    protected function loadRoutes(): void
    {
        Route::middleware(['web', 'auth'])
            ->prefix(config('rp.route_prefix', 'rp'))
            ->name('rp.')
            ->group(function () {
                require __DIR__ . '/../routes/web.php';
            });
    }

    /**
     * Load package views.
     */
    protected function loadViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'rp');
    }

    /**
     * Publish configuration file.
     */
    protected function publishConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../config/rp.php' => config_path('rp.php'),
        ], 'rp-config');
    }
}

