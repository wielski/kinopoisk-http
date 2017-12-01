<?php

namespace Siqwell\Kinopoisk;

use Illuminate\Support\ServiceProvider;

/**
 * Class KinopoiskServiceProvider
 * @package Siqwell\Kinopoisk
 */
class KinopoiskServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('kinopoisk.parser', function () {
            return new Client();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['kinopoisk.parser'];
    }
}