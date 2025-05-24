<?php

namespace Sebastienheyd\BoilerplateMediaManager;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Intervention\Image\Laravel\ServiceProvider as InterventionImageServiceProvider;
use Intervention\Image\Laravel\Facades\Image as ImageFacade;
use Sebastienheyd\BoilerplateMediaManager\Lib\ImageResizer;
use Sebastienheyd\BoilerplateMediaManager\View\Composers\FileComposer;
use Sebastienheyd\BoilerplateMediaManager\View\Composers\ImageComposer;

class ServiceProvider extends BaseServiceProvider
{
    protected $defer = false;
    protected $loader;

    /**
     * Create a new boilerplate service provider instance.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->loader = AliasLoader::getInstance();

        parent::__construct($app);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/config' => config_path('boilerplate'),
            ], ['boilerplate', 'boilerplate-config']);

            $this->publishes([
                __DIR__.'/public' => public_path('assets/vendor/boilerplate-media-manager'),
            ], ['boilerplate', 'boilerplate-public', 'laravel-assets']);

            $this->publishes([
                __DIR__.'/resources/lang' => resource_path('lang/vendor/boilerplate-media-manager'),
            ], 'boilerplate-media-manager-lang');

            $this->publishes([
                __DIR__.'/resources/views' => resource_path('views/vendor/boilerplate-media-manager'),
            ], 'boilerplate-media-manager-views');

            $this->commands([Commands\Clearthumbs::class]);
        }

        $this->loadRoutesFrom(__DIR__.'/routes/boilerplate-media-manager.php');
        $this->loadViewsFrom(__DIR__.'/resources/views/components', 'boilerplate-media-manager');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'boilerplate-media-manager');
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'boilerplate-media-manager');
        $this->loadMigrationsFrom(__DIR__.'/migrations');

        View::composer([
            'boilerplate-media-manager::image',
            'boilerplate-media-manager::components.image',
        ], ImageComposer::class);

        View::composer([
            'boilerplate-media-manager::file',
            'boilerplate-media-manager::components.file',
        ], FileComposer::class);

        Blade::directive('img', function ($options) {
            return "<?= img($options) ?>";
        });
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/mediamanager.php', 'boilerplate.mediamanager');
        app('boilerplate.menu.items')->registerMenuItem(Menu\BoilerplateMediaManager::class);
        config(['filesystems.disks.public.url' => config('boilerplate.mediamanager.base_url', '/storage')]);
        $this->registerIntervention();
    }

    /**
     * Register package intervention\image.
     */
    private function registerIntervention()
    {
        $this->app->register(InterventionImageServiceProvider::class);
        $this->loader->alias('Image', ImageFacade::class);
        $this->loader->alias('ImageResizer', ImageResizer::class);
    }
}
