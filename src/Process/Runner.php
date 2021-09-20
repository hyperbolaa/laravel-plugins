<?php

namespace Hyperbolaa\Plugins\Process;

use Hyperbolaa\Plugins\Contracts\RepositoryInterface;
use Hyperbolaa\Plugins\Contracts\RunableInterface;

class Runner implements RunableInterface
{
    /**
     * The plugin instance.
     * @var RepositoryInterface
     */
    protected $plugin;

    public function __construct(RepositoryInterface $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Run the given command.
     *
     * @param string $command
     */
    public function run($command)
    {
        passthru($command);
    }
}
