<?php

namespace Hyperbolaa\Plugins;

use Countable;
use Illuminate\Cache\CacheManager;
use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Hyperbolaa\Plugins\Contracts\RepositoryInterface;
use Hyperbolaa\Plugins\Exceptions\InvalidAssetPath;
use Hyperbolaa\Plugins\Exceptions\PluginNotFoundException;
use Hyperbolaa\Plugins\Process\Installer;
use Hyperbolaa\Plugins\Process\Updater;

abstract class FileRepository implements RepositoryInterface, Countable
{
    use Macroable;

    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application|\Laravel\Lumen\Application
     */
    protected $app;

    /**
     * The plugin path.
     *
     * @var string|null
     */
    protected $path;

    /**
     * The scanned paths.
     *
     * @var array
     */
    protected $paths = [];

    /**
     * @var string
     */
    protected $stubPath;
    /**
     * @var UrlGenerator
     */
    private $url;
    /**
     * @var ConfigRepository
     */
    private $config;
    /**
     * @var Filesystem
     */
    private $files;
    /**
     * @var CacheManager
     */
    private $cache;

    /**
     * The constructor.
     * @param Container $app
     * @param string|null $path
     */
    public function __construct(Container $app, $path = null)
    {
        $this->app = $app;
        $this->path = $path;
        $this->url = $app['url'];
        $this->config = $app['config'];
        $this->files = $app['files'];
        $this->cache = $app['cache'];
    }

    /**
     * Add other plugin location.
     *
     * @param string $path
     *
     * @return $this
     */
    public function addLocation($path)
    {
        $this->paths[] = $path;

        return $this;
    }

    /**
     * Get all additional paths.
     *
     * @return array
     */
    public function getPaths() : array
    {
        return $this->paths;
    }

    /**
     * Get scanned plugins paths.
     *
     * @return array
     */
    public function getScanPaths() : array
    {
        $paths = $this->paths;

        $paths[] = $this->getPath();

        if ($this->config('scan.enabled')) {
            $paths = array_merge($paths, $this->config('scan.paths'));
        }

        $paths = array_map(function ($path) {
            return Str::endsWith($path, '/*') ? $path : Str::finish($path, '/*');
        }, $paths);

        return $paths;
    }

    /**
     * Creates a new Plugin instance
     *
     * @param Container $app
     * @param string $args
     * @param string $path
     * @return \Hyperbolaa\Plugins\Plugin
     */
    abstract protected function createPlugin(...$args);

    /**
     * Get & scan all plugins.
     *
     * @return array
     */
    public function scan()
    {
        $paths = $this->getScanPaths();

        $plugins = [];

        foreach ($paths as $key => $path) {
            $manifests = $this->getFiles()->glob("{$path}/plugin.json");

            is_array($manifests) || $manifests = [];

            foreach ($manifests as $manifest) {
                $name = Json::make($manifest)->get('name');

                $plugins[$name] = $this->createPlugin($this->app, $name, dirname($manifest));
            }
        }

        return $plugins;
    }

    /**
     * Get all plugins.
     *
     * @return array
     */
    public function all() : array
    {
        if (!$this->config('cache.enabled')) {
            return $this->scan();
        }

        return $this->formatCached($this->getCached());
    }

    /**
     * Format the cached data as array of plugins.
     *
     * @param array $cached
     *
     * @return array
     */
    protected function formatCached($cached)
    {
        $plugins = [];

        foreach ($cached as $name => $module) {
            $path = $module['path'];

            $plugins[$name] = $this->createPlugin($this->app, $name, $path);
        }

        return $plugins;
    }

    /**
     * Get cached plugins.
     *
     * @return array
     */
    public function getCached()
    {
        return $this->cache->remember($this->config('cache.key'), $this->config('cache.lifetime'), function () {
            return $this->toCollection()->toArray();
        });
    }

    /**
     * Get all plugins as collection instance.
     *
     * @return Collection
     */
    public function toCollection() : Collection
    {
        return new Collection($this->scan());
    }

    /**
     * Get plugins by status.
     *
     * @param $status
     *
     * @return array
     */
    public function getByStatus($status) : array
    {
        $plugins = [];

        /** @var Module $module */
        foreach ($this->all() as $name => $module) {
            if ($module->isStatus($status)) {
                $plugins[$name] = $module;
            }
        }

        return $plugins;
    }

    /**
     * Determine whether the given plugin exist.
     *
     * @param $name
     *
     * @return bool
     */
    public function has($name) : bool
    {
        return array_key_exists($name, $this->all());
    }

    /**
     * Get list of enabled plugins.
     *
     * @return array
     */
    public function allEnabled() : array
    {
        return $this->getByStatus(true);
    }

    /**
     * Get list of disabled plugins.
     *
     * @return array
     */
    public function allDisabled() : array
    {
        return $this->getByStatus(false);
    }

    /**
     * Get count from all plugins.
     *
     * @return int
     */
    public function count() : int
    {
        return count($this->all());
    }

    /**
     * Get all ordered plugins.
     *
     * @param string $direction
     *
     * @return array
     */
    public function getOrdered($direction = 'asc') : array
    {
        $plugins = $this->allEnabled();

        uasort($plugins, function (Module $a, Module $b) use ($direction) {
            if ($a->get('priority') === $b->get('priority')) {
                return 0;
            }

            if ($direction === 'desc') {
                return $a->get('priority') < $b->get('priority') ? 1 : -1;
            }

            return $a->get('priority') > $b->get('priority') ? 1 : -1;
        });

        return $plugins;
    }

    /**
     * @inheritDoc
     */
    public function getPath() : string
    {
        return $this->path ?: $this->config('paths.plugins', base_path('Modules'));
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        foreach ($this->getOrdered() as $module) {
            $module->register();
        }
    }

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        foreach ($this->getOrdered() as $module) {
            $module->boot();
        }
    }

    /**
     * @inheritDoc
     */
    public function find(string $name)
    {
        foreach ($this->all() as $module) {
            if ($module->getLowerName() === strtolower($name)) {
                return $module;
            }
        }

        return;
    }

    /**
     * @inheritDoc
     */
    public function findByAlias(string $alias)
    {
        foreach ($this->all() as $module) {
            if ($module->getAlias() === $alias) {
                return $module;
            }
        }

        return;
    }

    /**
     * @inheritDoc
     */
    public function findRequirements($name): array
    {
        $requirements = [];

        $module = $this->findOrFail($name);

        foreach ($module->getRequires() as $requirementName) {
            $requirements[] = $this->findByAlias($requirementName);
        }

        return $requirements;
    }

    /**
     * Find a specific plugin, if there return that, otherwise throw exception.
     *
     * @param $name
     *
     * @return Module
     *
     * @throws PluginNotFoundException
     */
    public function findOrFail(string $name)
    {
        $module = $this->find($name);

        if ($module !== null) {
            return $module;
        }

        throw new PluginNotFoundException("Module [{$name}] does not exist!");
    }

    /**
     * Get all plugins as laravel collection instance.
     *
     * @param $status
     *
     * @return Collection
     */
    public function collections($status = 1) : Collection
    {
        return new Collection($this->getByStatus($status));
    }

    /**
     * Get plugin path for a specific plugin.
     *
     * @param $module
     *
     * @return string
     */
    public function getPluginPath($module)
    {
        try {
            return $this->findOrFail($module)->getPath() . '/';
        } catch (PluginNotFoundException $e) {
            return $this->getPath() . '/' . Str::studly($module) . '/';
        }
    }

    /**
     * @inheritDoc
     */
    public function assetPath(string $module) : string
    {
        return $this->config('paths.assets') . '/' . $module;
    }

    /**
     * @inheritDoc
     */
    public function config(string $key, $default = null)
    {
        return $this->config->get('plugins.' . $key, $default);
    }

    /**
     * Get storage path for plugin used.
     *
     * @return string
     */
    public function getUsedStoragePath() : string
    {
        $directory = storage_path('app/plugins');
        if ($this->getFiles()->exists($directory) === false) {
            $this->getFiles()->makeDirectory($directory, 0777, true);
        }

        $path = storage_path('app/plugins/plugins.used');
        if (!$this->getFiles()->exists($path)) {
            $this->getFiles()->put($path, '');
        }

        return $path;
    }

    /**
     * Set plugin used for cli session.
     *
     * @param $name
     *
     * @throws PluginNotFoundException
     */
    public function setUsed($name)
    {
        $module = $this->findOrFail($name);

        $this->getFiles()->put($this->getUsedStoragePath(), $module);
    }

    /**
     * Forget the plugin used for cli session.
     */
    public function forgetUsed()
    {
        if ($this->getFiles()->exists($this->getUsedStoragePath())) {
            $this->getFiles()->delete($this->getUsedStoragePath());
        }
    }

    /**
     * Get plugin used for cli session.
     * @return string
     * @throws \Hyperbolaa\Plugins\Exceptions\PluginNotFoundException
     */
    public function getUsedNow() : string
    {
        return $this->findOrFail($this->getFiles()->get($this->getUsedStoragePath()));
    }

    /**
     * Get laravel filesystem instance.
     *
     * @return Filesystem
     */
    public function getFiles(): Filesystem
    {
        return $this->files;
    }

    /**
     * Get plugin assets path.
     *
     * @return string
     */
    public function getAssetsPath() : string
    {
        return $this->config('paths.assets');
    }

    /**
     * Get asset url from a specific plugin.
     * @param string $asset
     * @return string
     * @throws InvalidAssetPath
     */
    public function asset($asset) : string
    {
        if (Str::contains($asset, ':') === false) {
            throw InvalidAssetPath::missingPluginName($asset);
        }
        list($name, $url) = explode(':', $asset);

        $baseUrl = str_replace(public_path() . DIRECTORY_SEPARATOR, '', $this->getAssetsPath());

        $url = $this->url->asset($baseUrl . "/{$name}/" . $url);

        return str_replace(['http://', 'https://'], '//', $url);
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(string $name) : bool
    {
        return $this->findOrFail($name)->isEnabled();
    }

    /**
     * @inheritDoc
     */
    public function isDisabled(string $name) : bool
    {
        return !$this->isEnabled($name);
    }

    /**
     * Enabling a specific plugin.
     * @param string $name
     * @return void
     * @throws \Hyperbolaa\Plugins\Exceptions\PluginNotFoundException
     */
    public function enable($name)
    {
        $this->findOrFail($name)->enable();
    }

    /**
     * Disabling a specific plugin.
     * @param string $name
     * @return void
     * @throws \Hyperbolaa\Plugins\Exceptions\PluginNotFoundException
     */
    public function disable($name)
    {
        $this->findOrFail($name)->disable();
    }

    /**
     * @inheritDoc
     */
    public function delete(string $name) : bool
    {
        return $this->findOrFail($name)->delete();
    }

    /**
     * Update dependencies for the specified plugin.
     *
     * @param string $module
     */
    public function update($module)
    {
        with(new Updater($this))->update($module);
    }

    /**
     * Install the specified plugin.
     *
     * @param string $name
     * @param string $version
     * @param string $type
     * @param bool   $subtree
     *
     * @return \Symfony\Component\Process\Process
     */
    public function install($name, $version = 'dev-master', $type = 'composer', $subtree = false)
    {
        $installer = new Installer($name, $version, $type, $subtree);

        return $installer->run();
    }

    /**
     * Get stub path.
     *
     * @return string|null
     */
    public function getStubPath()
    {
        if ($this->stubPath !== null) {
            return $this->stubPath;
        }

        if ($this->config('stubs.enabled') === true) {
            return $this->config('stubs.path');
        }

        return $this->stubPath;
    }

    /**
     * Set stub path.
     *
     * @param string $stubPath
     *
     * @return $this
     */
    public function setStubPath($stubPath)
    {
        $this->stubPath = $stubPath;

        return $this;
    }
}
