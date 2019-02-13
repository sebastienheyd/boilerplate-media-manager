<?php

namespace Sebastienheyd\BoilerplateMediaManager;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class BoilerplateMediaManagerServiceProvider extends ServiceProvider
{
    protected $defer = false;
    protected $loader;

    /**
     * Create a new boilerplate service provider instance.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    public function __construct($app)
    {
        $this->loader = AliasLoader::getInstance();
        return parent::__construct($app);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__.'/config' => config_path()], 'config');
        $this->publishes([__DIR__.'/public' => public_path('assets/vendor/boilerplate-media-manager')], 'public');

        // If routes file has been published, load routes from the published file
        $routesPath = base_path('routes/boilerplate-media-manager.php');
        $this->loadRoutesFrom(is_file($routesPath) ? $routesPath : __DIR__.'/routes/boilerplate-media-manager.php');

        // Load views and translations from current directory
        $this->loadViewsFrom(__DIR__.'/resources/views', 'boilerplate-media-manager');
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'boilerplate-media-manager');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/mediamanager.php', 'mediamanager');

        config([
            'boilerplate.menu.providers' => array_merge(
                config('boilerplate.menu.providers'), [
                \Sebastienheyd\BoilerplateMediaManager\Menu\BoilerplateMediaManager::class])
        ]);

        $this->registerIntervention();
    }

    /**
     * Register package intervention\image
     */
    private function registerIntervention()
    {
        $this->app->register(\Intervention\Image\ImageServiceProvider::class);
        $this->loader->alias('Image', \Intervention\Image\Facades\Image::class);
    }
}
