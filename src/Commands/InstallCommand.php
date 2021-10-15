<?php

namespace Hyperbolaa\Plugins\Commands;

use Illuminate\Console\Command;
use Hyperbolaa\Plugins\Json;
use Hyperbolaa\Plugins\Process\Installer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class InstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the specified package by given package name (vendor/name).';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle() : int
    {
        $this->install(
            $this->argument('name'),
            $this->argument('version')
        );

        return 0;
    }

    /**
     * Install the package.
     *
     * @param string $name
     * @param string $version
     */
    protected function install($name, $version = 'dev-master')
    {
        $installer = new Installer($name, $version);

        $installer->setConsole($this);

        if ($timeout = $this->option('timeout')) {
            $installer->setTimeout($timeout);
        }

        $installer->run();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::OPTIONAL, 'The name of package will be installed.'],
            ['version', InputArgument::OPTIONAL, 'The version of package will be installed.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['timeout', null, InputOption::VALUE_OPTIONAL, 'The process timeout.', null],
        ];
    }
}
