<?php

namespace Hyperbolaa\Plugins\Commands;

use Illuminate\Console\Command;
use Hyperbolaa\Plugins\Plugin;
use Symfony\Component\Console\Input\InputArgument;

class EnableCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:enable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable the specified module.';

    /**
     * Execute the console command.
     */
    public function handle() : int
    {
        /**
         * check if user entred an argument
         */
        if ($this->argument('module') === null) {
            $this->enableAll();

            return 0;
        }

        /** @var Plugin $module */
        $module = $this->laravel['plugins']->findOrFail($this->argument('module'));

        if ($module->isDisabled()) {
            $module->enable();

            $this->info("Module [{$module}] enabled successful.");
        } else {
            $this->comment("Module [{$module}] has already enabled.");
        }

        return 0;
    }

    /**
     * enableAll
     *
     * @return void
     */
    public function enableAll()
    {
        /** @var Plugin $modules */
        $modules = $this->laravel['plugins']->all();

        foreach ($modules as $module) {
            if ($module->isDisabled()) {
                $module->enable();

                $this->info("Module [{$module}] enabled successful.");
            } else {
                $this->comment("Module [{$module}] has already enabled.");
            }
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['module', InputArgument::OPTIONAL, 'Module name.'],
        ];
    }
}
