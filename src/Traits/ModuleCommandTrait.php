<?php

namespace Hyperbolaa\Plugins\Traits;

trait ModuleCommandTrait
{
    /**
     * Get the module name.
     *
     * @return string
     */
    public function getModuleName()
    {
        $module = $this->argument('plugin') ?: app('plugins')->getUsedNow();

        $module = app('plugins')->findOrFail($module);

        return $module->getStudlyName();
    }
}
