<?php

namespace Hyperbolaa\Plugins\Traits;

trait PluginCommandTrait
{
    /**
     * Get the plugin name.
     *
     * @return string
     */
    public function getPluginName()
    {
        $plugin = $this->argument('plugin') ?: app('plugins')->getUsedNow();

        $plugin = app('plugins')->findOrFail($plugin);

        return $plugin->getStudlyName();
    }
}
