<?php

namespace Hyperbolaa\Plugins;

use Hyperbolaa\Plugins\Contracts\RepositoryInterface;
use Hyperbolaa\Plugins\Exceptions\InvalidActivatorClass;
use Hyperbolaa\Plugins\Support\Stub;

class LaravelPluginsServiceProvider extends PluginsServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot()
    {
        $this->registerNamespaces();
        $this->registerPlugins();
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerServices();
        $this->setupStubPath();
        $this->registerProviders();

        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'plugins');
    }

    /**
     * Setup stub path.
     */
    public function setupStubPath()
    {
        $path = $this->app['config']->get('plugins.stubs.path') ?? __DIR__ . '/Commands/stubs';
        Stub::setBasePath($path);

        $this->app->booted(function ($app) {
            /** @var RepositoryInterface $pluginRepository */
            $pluginRepository = $app[RepositoryInterface::class];
            if ($pluginRepository->config('stubs.enabled') === true) {
                Stub::setBasePath($pluginRepository->config('stubs.path'));
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function registerServices()
    {
        $this->app->singleton(Contracts\RepositoryInterface::class, function ($app) {
            $path = $app['config']->get('plugins.paths.plugins');
            echo $path;exit;
            return new Laravel\LaravelFileRepository($app, $path);
        });
        $this->app->singleton(Contracts\ActivatorInterface::class, function ($app) {
            $activator = $app['config']->get('plugins.activator');
            $class = $app['config']->get('plugins.activators.' . $activator)['class'];

            if ($class === null) {
                throw InvalidActivatorClass::missingConfig();
            }

            return new $class($app);
        });
        $this->app->alias(Contracts\RepositoryInterface::class, 'plugins');
    }
}
