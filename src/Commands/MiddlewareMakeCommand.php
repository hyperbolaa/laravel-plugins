<?php

namespace Hyperbolaa\Plugins\Commands;

use Illuminate\Support\Str;
use Hyperbolaa\Plugins\Support\Config\GenerateConfigReader;
use Hyperbolaa\Plugins\Support\Stub;
use Hyperbolaa\Plugins\Traits\PluginCommandTrait;
use Symfony\Component\Console\Input\InputArgument;

class MiddlewareMakeCommand extends GeneratorCommand
{
    use PluginCommandTrait;

    /**
     * The name of argument name.
     *
     * @var string
     */
    protected $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:make-middleware';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new middleware class for the specified plugin.';

    public function getDefaultNamespace() : string
    {
        $plugin = $this->laravel['plugins'];

        return $plugin->config('paths.generator.filter.namespace') ?: $plugin->config('paths.generator.filter.path', 'Http/Middleware');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the command.'],
            ['plugin', InputArgument::OPTIONAL, 'The name of plugin will be used.'],
        ];
    }

    /**
     * @return mixed
     */
    protected function getTemplateContents()
    {
        $plugin = $this->laravel['plugins']->findOrFail($this->getPluginName());

        return (new Stub('/middleware.stub', [
            'NAMESPACE' => $this->getClassNamespace($plugin),
            'CLASS'     => $this->getClass(),
        ]))->render();
    }

    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['plugins']->getPluginPath($this->getPluginName());

        $middlewarePath = GenerateConfigReader::read('filter');

        return $path . $middlewarePath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return Str::studly($this->argument('name'));
    }
}
