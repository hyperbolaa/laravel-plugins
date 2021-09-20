<?php

namespace Hyperbolaa\Plugins\Commands;

use Illuminate\Console\Command;
use Hyperbolaa\Plugins\Traits\PluginCommandTrait;
use Symfony\Component\Console\Input\InputArgument;

class UpdateCommand extends Command
{
    use PluginCommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update dependencies for the specified plugin or for all plugins.';

    /**
     * Execute the console command.
     */
    public function handle() : int
    {
        $name = $this->argument('plugin');

        if ($name) {
            $this->updateModule($name);

            return 0;
        }

        /** @var \Hyperbolaa\Plugins\Plugin $plugin */
        foreach ($this->laravel['plugins']->getOrdered() as $module) {
            $this->updateModule($module->getName());
        }

        return 0;
    }

    protected function updateModule($name)
    {
        $this->line('Running for plugin: <info>' . $name . '</info>');

        $this->laravel['plugins']->update($name);

        $this->info("Module [{$name}] updated successfully.");
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['plugin', InputArgument::OPTIONAL, 'The name of plugin will be updated.'],
        ];
    }
}
