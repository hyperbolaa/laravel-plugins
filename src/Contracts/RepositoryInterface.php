<?php

namespace Hyperbolaa\Plugins\Contracts;

use Hyperbolaa\Plugins\Exceptions\PluginNotFoundException;
use Hyperbolaa\Plugins\Plugin;

interface RepositoryInterface
{
    /**
     * Get all modules.
     *
     * @return mixed
     */
    public function all();

    /**
     * Get cached modules.
     *
     * @return array
     */
    public function getCached();

    /**
     * Scan & get all available modules.
     *
     * @return array
     */
    public function scan();

    /**
     * Get modules as modules collection instance.
     *
     * @return \Hyperbolaa\Plugins\Collection
     */
    public function toCollection();

    /**
     * Get scanned paths.
     *
     * @return array
     */
    public function getScanPaths();

    /**
     * Get list of enabled modules.
     *
     * @return mixed
     */
    public function allEnabled();

    /**
     * Get list of disabled modules.
     *
     * @return mixed
     */
    public function allDisabled();

    /**
     * Get count from all modules.
     *
     * @return int
     */
    public function count();

    /**
     * Get all ordered modules.
     * @param string $direction
     * @return mixed
     */
    public function getOrdered($direction = 'asc');

    /**
     * Get modules by the given status.
     *
     * @param int $status
     *
     * @return mixed
     */
    public function getByStatus($status);

    /**
     * Find a specific plugin.
     *
     * @param $name
     * @return Plugin|null
     */
    public function find(string $name);

    /**
     * Find all modules that are required by a plugin. If the plugin cannot be found, throw an exception.
     *
     * @param $name
     * @return array
     * @throws PluginNotFoundException
     */
    public function findRequirements($name): array;

    /**
     * Find a specific plugin. If there return that, otherwise throw exception.
     *
     * @param $name
     *
     * @return mixed
     */
    public function findOrFail(string $name);

    public function getPluginPath($moduleName);

    /**
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFiles();

    /**
     * Get a specific config data from a configuration file.
     * @param string $key
     *
     * @param string|null $default
     * @return mixed
     */
    public function config(string $key, $default = null);

    /**
     * Get a plugin path.
     *
     * @return string
     */
    public function getPath() : string;

    /**
     * Find a specific plugin by its alias.
     * @param string $alias
     * @return Plugin|void
     */
    public function findByAlias(string $alias);

    /**
     * Boot the modules.
     */
    public function boot(): void;

    /**
     * Register the modules.
     */
    public function register(): void;

    /**
     * Get asset path for a specific plugin.
     *
     * @param string $module
     * @return string
     */
    public function assetPath(string $module): string;

    /**
     * Delete a specific plugin.
     * @param string $module
     * @return bool
     * @throws \Hyperbolaa\Plugins\Exceptions\PluginNotFoundException
     */
    public function delete(string $module): bool;

    /**
     * Determine whether the given plugin is activated.
     * @param string $name
     * @return bool
     * @throws PluginNotFoundException
     */
    public function isEnabled(string $name) : bool;

    /**
     * Determine whether the given plugin is not activated.
     * @param string $name
     * @return bool
     * @throws PluginNotFoundException
     */
    public function isDisabled(string $name) : bool;
}
