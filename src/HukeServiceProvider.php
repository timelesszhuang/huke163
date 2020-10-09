<?php

namespace qiangbi\huke163;


use Illuminate\Support\ServiceProvider;

class HukeServiceProvider extends ServiceProvider
{


    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     *
     * @return void
     */
    public function boot()
    {
        $configPath = realpath(__DIR__ . '/../config/huke.php');
        $this->publishes([$configPath => config_path('huke.php')], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configPath = realpath(__DIR__ . '/../config/huke.php');
        $this->mergeConfigFrom($configPath, 'huke');
        $this->publishes([$configPath => config_path('huke.php')], 'config');
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {

    }

}
