<?php

namespace Hyperbolaa\Plugins\Commands;

use Illuminate\Console\Command;
use Hyperbolaa\Plugins\Plugin;
use Symfony\Component\Console\Input\InputArgument;

class DisableCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:disable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable the specified plugin.';

    /**
     * Execute the console command.
     */
    public function handle() : int
    {
        /**
         * check if user entred an argument
         */
        if ($this->argument('plugin') === null) {
            $this->disableAll();
        }

        /** @var Plugin $plugin */
        $plugin = $this->laravel['plugins']->findOrFail($this->argument('plugin'));

        if ($plugin->isEnabled()) {
            $plugin->disable();

            $this->info("Module [{$plugin}] disabled successful.");
        } else {
            $this->comment("Module [{$plugin}] has already disabled.");
        }

        return 0;
    }

    /**
     * disableAll
     *
     * @return void
     */
    public function disableAll()
    {
        /** @var Plugin $plugins */
        $plugins = $this->laravel['plugins']->all();

        foreach ($plugins as $plugin) {
            if ($plugin->isEnabled()) {
                $plugin->disable();

                $this->info("Module [{$plugin}] disabled successful.");
            } else {
                $this->comment("Module [{$plugin}] has already disabled.");
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
            ['plugin', InputArgument::OPTIONAL, 'Module name.'],
        ];
    }
}
