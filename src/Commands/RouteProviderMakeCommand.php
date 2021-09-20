<?php

namespace Hyperbolaa\Plugins\Commands;

use Hyperbolaa\Plugins\Support\Config\GenerateConfigReader;
use Hyperbolaa\Plugins\Support\Stub;
use Hyperbolaa\Plugins\Traits\PluginCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class RouteProviderMakeCommand extends GeneratorCommand
{
    use PluginCommandTrait;

    protected $argumentName = 'plugin';

    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'plugin:route-provider';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Create a new route service provider for the specified plugin.';

    /**
     * The command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['plugin', InputArgument::OPTIONAL, 'The name of plugin will be used.'],
        ];
    }

    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when the file already exists.'],
        ];
    }

    /**
     * Get template contents.
     *
     * @return string
     */
    protected function getTemplateContents()
    {
        $plugin = $this->laravel['plugins']->findOrFail($this->getPluginName());

        return (new Stub('/route-provider.stub', [
            'NAMESPACE'            => $this->getClassNamespace($plugin),
            'CLASS'                => $this->getFileName(),
            'PLUGIN_NAMESPACE'     => $this->laravel['plugins']->config('namespace'),
            'PLUGIN'               => $this->getPluginName(),
            'CONTROLLER_NAMESPACE' => $this->getControllerNameSpace(),
            'WEB_ROUTES_PATH'      => $this->getWebRoutesPath(),
            'API_ROUTES_PATH'      => $this->getApiRoutesPath(),
            'LOWER_NAME'           => $plugin->getLowerName(),
        ]))->render();
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return 'RouteServiceProvider';
    }

    /**
     * Get the destination file path.
     *
     * @return string
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['plugins']->getPluginPath($this->getPluginName());

        $generatorPath = GenerateConfigReader::read('provider');

        return $path . $generatorPath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * @return mixed
     */
    protected function getWebRoutesPath()
    {
        return '/' . $this->laravel['plugins']->config('stubs.files.routes/web', 'Routes/web.php');
    }

    /**
     * @return mixed
     */
    protected function getApiRoutesPath()
    {
        return '/' . $this->laravel['plugins']->config('stubs.files.routes/api', 'Routes/api.php');
    }

    public function getDefaultNamespace() : string
    {
        $plugin = $this->laravel['plugins'];

        return $plugin->config('paths.generator.provider.namespace') ?: $plugin->config('paths.generator.provider.path', 'Providers');
    }

    /**
     * @return string
     */
    private function getControllerNameSpace(): string
    {
        $plugin = $this->laravel['plugins'];

        return str_replace('/', '\\', $plugin->config('paths.generator.controller.namespace') ?: $plugin->config('paths.generator.controller.path', 'Controller'));
    }
}
