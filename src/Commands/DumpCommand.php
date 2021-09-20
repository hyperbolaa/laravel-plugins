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

        if ($module = $this->argument('plugin')) {
            $this->dump($module);
        } else {
            foreach ($this->laravel['plugins']->all() as $module) {
                $this->dump($module->getStudlyName());
            }
        }

        return 0;
    }

    public function dump($module)
    {
        $module = $this->laravel['plugins']->findOrFail($module);

        $this->line("<comment>Running for plugin</comment>: {$module}");

        chdir($module->getPath());

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
            ['plugin', InputArgument::OPTIONAL, 'Module name.'],
        ];
    }
}
