<?php

namespace TaylorNetwork\UsernameGenerator;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/config/username_generator.php', 'username_generator');

        $this->publishes([
            __DIR__.'/config/username_generator.php' => config_path('username_generator.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('UsernameGenerator', Generator::class);
    }
}
