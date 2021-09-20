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
    protected $name = 'plugin:enable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable the specified plugin.';

    /**
     * Execute the console command.
     */
    public function handle() : int
    {
        /**
         * check if user entred an argument
         */
        if ($this->argument('plugin') === null) {
            $this->enableAll();

            return 0;
        }

        /** @var Plugin $plugin */
        $plugin = $this->laravel['plugins']->findOrFail($this->argument('plugin'));

        if ($plugin->isDisabled()) {
            $plugin->enable();

            $this->info("Plugin [{$plugin}] enabled successful.");
        } else {
            $this->comment("Plugin [{$plugin}] has already enabled.");
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
        /** @var Plugin $plugins */
        $plugins = $this->laravel['plugins']->all();

        foreach ($plugins as $plugin) {
            if ($plugin->isDisabled()) {
                $plugin->enable();

                $this->info("Plugin [{$plugin}] enabled successful.");
            } else {
                $this->comment("Plugin [{$plugin}] has already enabled.");
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
            ['plugin', InputArgument::OPTIONAL, 'Plugin name.'],
        ];
    }
}
