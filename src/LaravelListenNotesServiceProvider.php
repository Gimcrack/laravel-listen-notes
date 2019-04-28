<?php

namespace Ingenious\LaravelListenNotes;

use Illuminate\Support\ServiceProvider;

class LaravelListenNotesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        // config
         $this->publishes([
             __DIR__.'/config/laravel-listen-notes.php' => config_path('laravel-listen-notes.php')
         ], 'config');

         // migrations
        $this->publishes([
           __DIR__.'/Database/migrations' => database_path('migrations')
        ], 'migrations');


        // factories
        $this->registerFactories(__DIR__ . "/Database/factories");

        // migrations
        //$this->loadMigrationsFrom(__DIR__ . "/Database/migrations");
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('PodcastSearch', function() {
            return new LaravelListenNotes;
        } );
    }

    /**
     * Register the package factories from the specified path
     *
     * @param  string $path
     * @return void
     */
    protected function registerFactories($path)
    {
        $factory = app(\Illuminate\Database\Eloquent\Factory::class);

        foreach (glob($path.'/*.php') as $file) {
            require $file;
        }
    }
}
