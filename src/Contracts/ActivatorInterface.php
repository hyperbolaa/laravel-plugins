<?php

namespace Hyperbolaa\Plugins\Contracts;

use Hyperbolaa\Plugins\Plugin;

interface ActivatorInterface
{
    /**
     * Enables a plugin
     *
     * @param Plugin $plugin
     */
    public function enable(Plugin $plugin): void;

    /**
     * Disables a plugin
     *
     * @param Plugin $plugin
     */
    public function disable(Plugin $plugin): void;

    /**
     * Determine whether the given status same with a plugin status.
     *
     * @param Plugin $plugin
     * @param bool $status
     *
     * @return bool
     */
    public function hasStatus(Plugin $plugin, bool $status): bool;

    /**
     * Set active state for a plugin.
     *
     * @param Plugin $plugin
     * @param bool $active
     */
    public function setActive(Plugin $plugin, bool $active): void;

    /**
     * Sets a plugin status by its name
     *
     * @param  string $name
     * @param  bool $active
     */
    public function setActiveByName(string $name, bool $active): void;

    /**
     * Deletes a plugin activation status
     *
     * @param  Plugin $plugin
     */
    public function delete(Plugin $plugin): void;

    /**
     * Deletes any plugin activation statuses created by this class.
     */
    public function reset(): void;
}
