<?php

namespace Juddling\PHPStorm;

use Illuminate\Support\ServiceProvider;

class LaunchUrlServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                LaunchUrlCommand::class,
            ]);
        }
    }

    public function register()
    {

    }
}