<?php

namespace EvolutionCMS\ClientSettings;

use EvolutionCMS\ServiceProvider;

class ClientSettingsServiceProvider extends ServiceProvider
{
    protected $namespace = 'cs';

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('clientsettings', ClientSettings::class);

        $this->loadViewsFrom(dirname(__DIR__) . '/views', $this->namespace);

        $this->loadPluginsFrom(dirname(__DIR__) . '/plugins/');

        $this->publishes([
            dirname(__DIR__) . '/resources/seeders' => EVO_CORE_PATH . 'database/seeders',
            dirname(__DIR__) . '/resources/examples' => EVO_CORE_PATH . 'custom/clientsettings',
        ]);
    }

    public function boot()
    {
        $this->loadTranslationsFrom(dirname(__DIR__) . '/lang', $this->namespace);

        $this->app->registerRoutingModule('ClientSettings', dirname(__DIR__) . '/routes.php');
    }
}