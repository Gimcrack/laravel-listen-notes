<?php

namespace Ingenious\LaravelListenNotes;

use Illuminate\Support\ServiceProvider;

class LaravelListenNotesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // config
         $this->publishes([
             __DIR__.'/config/laravel-listen-notes.php' => config_path('laravel-listen-notes.php')
         ], 'config');

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('LaravelListenNotes', function() {
            return new LaravelListenNotes;
        } );
    }
}
