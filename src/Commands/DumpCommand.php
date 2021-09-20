<?php

namespace Hyperbolaa\Plugins\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class DumpCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dump-autoload the specified plugin or for all plugin.';

    /**
     * Execute the console command.
     */
    public function handle() : int
    {
        $this->info('Generating optimized autoload plugins.');

        if ($plugin = $this->argument('plugin')) {
            $this->dump($plugin);
        } else {
            foreach ($this->laravel['plugins']->all() as $plugin) {
                $this->dump($plugin->getStudlyName());
            }
        }

        return 0;
    }

    public function dump($plugin)
    {
        $plugin = $this->laravel['plugins']->findOrFail($plugin);

        $this->line("<comment>Running for plugin</comment>: {$plugin}");

        chdir($plugin->getPath());

        passthru('composer dump -o -n -q');
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
