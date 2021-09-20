<?php

namespace Hyperbolaa\Plugins\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PublishConfigurationCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:publish-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish a plugin\'s config files to the application';

    /**
     * Execute the console command.
     */
    public function handle() : int
    {
        if ($module = $this->argument('plugin')) {
            $this->publishConfiguration($module);

            return 0;
        }

        foreach ($this->laravel['plugins']->allEnabled() as $module) {
            $this->publishConfiguration($module->getName());
        }

        return 0;
    }

    /**
     * @param string $module
     * @return string
     */
    private function getServiceProviderForModule($module)
    {
        $namespace = $this->laravel['config']->get('plugins.namespace');
        $studlyName = Str::studly($module);

        return "$namespace\\$studlyName\\Providers\\{$studlyName}ServiceProvider";
    }

    /**
     * @param string $module
     */
    private function publishConfiguration($module)
    {
        $this->call('vendor:publish', [
            '--provider' => $this->getServiceProviderForModule($module),
            '--force' => $this->option('force'),
            '--tag' => ['config'],
        ]);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['plugin', InputArgument::OPTIONAL, 'The name of plugin being used.'],
        ];
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['--force', '-f', InputOption::VALUE_NONE, 'Force the publishing of config files'],
        ];
    }
}
