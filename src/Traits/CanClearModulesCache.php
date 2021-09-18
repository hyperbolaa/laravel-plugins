<?php

namespace Hyperbolaa\Plugins\Traits;

trait CanClearModulesCache
{
    /**
     * Clear the modules cache if it is enabled
     */
    public function clearCache()
    {
        if (config('plugins.cache.enabled') === true) {
            app('cache')->forget(config('plugins.cache.key'));
        }
    }
}
