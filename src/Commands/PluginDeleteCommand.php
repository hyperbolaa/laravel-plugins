<?php

namespace Hyperbolaa\Plugins\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class PluginDeleteCommand extends Command
{
    protected $name = 'plugin:delete';
    protected $description = 'Delete a plugin from the application';

    public function handle() : int
    {
        $this->laravel['plugins']->delete($this->argument('plugin'));

        $this->info("Module {$this->argument('plugin')} has been deleted.");

        return 0;
    }

    protected function getArguments()
    {
        return [
            ['plugin', InputArgument::REQUIRED, 'The name of plugin to delete.'],
        ];
    }
}
