<?php

namespace Hyperbolaa\Plugins\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Hyperbolaa\Plugins\Commands;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * The available commands
     * @var array
     */
    protected $commands = [
        Commands\CommandMakeCommand::class,
        Commands\ControllerMakeCommand::class,
        Commands\DisableCommand::class,
        Commands\EnableCommand::class,
        Commands\MiddlewareMakeCommand::class,
        Commands\ProviderMakeCommand::class,
        Commands\RouteProviderMakeCommand::class,
        Commands\ListCommand::class,
        Commands\PluginDeleteCommand::class,
        Commands\PluginMakeCommand::class,
        Commands\RequestMakeCommand::class,
        Commands\RuleMakeCommand::class,
        Commands\PublishCommand::class,
        Commands\PublishConfigurationCommand::class,
        Commands\PublishTranslationCommand::class,
        Commands\SetupCommand::class,
        Commands\UnUseCommand::class,
        Commands\InstallCommand::class,
        Commands\UpdateCommand::class,
        Commands\UseCommand::class,
        Commands\ResourceMakeCommand::class,
        Commands\TestMakeCommand::class,
        Commands\ComponentClassMakeCommand::class,
        Commands\ComponentViewMakeCommand::class,
    ];

    public function register(): void
    {
        $this->commands(config('plugins.commands', $this->commands));
    }

    public function provides(): array
    {
        return $this->commands;
    }
}
