<?php

namespace Hyperbolaa\Plugins\Commands;

use Illuminate\Console\Command;
use Hyperbolaa\Plugins\Plugin;
use Symfony\Component\Console\Input\InputOption;

class ListCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show list of all plugins.';

    /**
     * Execute the console command.
     */
    public function handle() : int
    {
        $this->table(['Name', 'Status', 'Priority', 'Path'], $this->getRows());

        return 0;
    }

    /**
     * Get table rows.
     *
     * @return array
     */
    public function getRows()
    {
        $rows = [];

        /** @var Plugin $plugin */
        foreach ($this->getModules() as $module) {
            $rows[] = [
                $module->getName(),
                $module->isEnabled() ? 'Enabled' : 'Disabled',
                $module->get('priority'),
                $module->getPath(),
            ];
        }

        return $rows;
    }

    public function getModules()
    {
        switch ($this->option('only')) {
            case 'enabled':
                return $this->laravel['plugins']->getByStatus(1);
                break;

            case 'disabled':
                return $this->laravel['plugins']->getByStatus(0);
                break;

            case 'priority':
                return $this->laravel['plugins']->getPriority($this->option('direction'));
                break;

            default:
                return $this->laravel['plugins']->all();
                break;
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['only', 'o', InputOption::VALUE_OPTIONAL, 'Types of plugins will be displayed.', null],
            ['direction', 'd', InputOption::VALUE_OPTIONAL, 'The direction of ordering.', 'asc'],
        ];
    }
}
