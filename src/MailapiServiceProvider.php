<?php

namespace Exonos\Mailapi;

use Exonos\Mailapi\commands\ClientCommand;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class MailapiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoutes();
        $this->registerResources();
        $this->registerPublishing();
        $this->registerCommands();
    }

    /**
     * Register the Passport Artisan commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ClientCommand::class
            ]);
        }
    }

    /**
     * Register the Passport routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        if (Mailapi::$registersRoutes) {
            Route::group([
                'prefix' => 'api',
                'as' => 'mailapi.',
                'middleware' => [
                    'throttle:'.config('mailapi.throttle'),
                    \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
                    \Illuminate\Routing\Middleware\SubstituteBindings::class,
                ]
            ], function () {
                $this->loadRoutesFrom(__DIR__.'/routes/api.php');
            });
        }
    }

    /**
     * Register the Passport resources.
     *
     * @return void
     */
    protected function registerResources()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'passport');
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $publishesMigrationsMethod = method_exists($this, 'publishesMigrations')
                ? 'publishesMigrations'
                : 'publishes';

            $this->{$publishesMigrationsMethod}([
                __DIR__.'/database/migrations' => database_path('migrations'),
            ], 'mailapi-migrations');

            $this->publishes([
                __DIR__.'/resources/views' => base_path('resources/views/vendor/mailapi'),
            ], 'mailapi-views');

            $this->publishes([
                __DIR__.'/config/mailapi.php' => config_path('mailapi.php'),
            ], 'mailapi-config');
        }
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/config.php', 'mailapi');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }
}