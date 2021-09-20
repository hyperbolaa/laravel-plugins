<?php

namespace Hyperbolaa\Plugins\Commands;

use Illuminate\Support\Str;
use Hyperbolaa\Plugins\Support\Config\GenerateConfigReader;
use Hyperbolaa\Plugins\Support\Stub;
use Hyperbolaa\Plugins\Traits\PluginCommandTrait;
use Symfony\Component\Console\Input\InputArgument;

class ComponentClassMakeCommand extends GeneratorCommand
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
    protected $name = 'plugin:make-component';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new component-class for the specified plugin.';

    public function handle() : int
    {
        if (parent::handle() === E_ERROR) {
            return E_ERROR;
        }
        $this->writeComponentViewTemplate();

        return 0;
    }
    /**
     * Write the view template for the component.
     *
     * @return void
     */
    protected function writeComponentViewTemplate()
    {
        $this->call('plugin:make-component-view', ['name' => $this->argument('name') , 'plugin' => $this->argument('plugin')]);
    }

    public function getDefaultNamespace() : string
    {
        $plugin = $this->laravel['plugins'];

        return $plugin->config('paths.generator.component-class.namespace') ?: $plugin->config('paths.generator.component-class.path', 'View/Component');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the component.'],
            ['plugin', InputArgument::OPTIONAL, 'The name of plugin will be used.'],
        ];
    }
    /**
     * @return mixed
     */
    protected function getTemplateContents()
    {
        $plugin = $this->laravel['plugins']->findOrFail($this->getPluginName());

        return (new Stub('/component-class.stub', [
            'NAMESPACE'         => $this->getClassNamespace($plugin),
            'CLASS'             => $this->getClass(),
            'LOWER_NAME'        => $plugin->getLowerName(),
            'COMPONENT_NAME'    => 'components.' . Str::lower($this->argument('name')),
        ]))->render();
    }

    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['plugins']->getPluginPath($this->getPluginName());
        $factoryPath = GenerateConfigReader::read('component-class');

        return $path . $factoryPath->getPath() . '/' . $this->getFileName();
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return Str::studly($this->argument('name')) . '.php';
    }
}
