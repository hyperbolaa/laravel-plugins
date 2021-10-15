<?php

namespace Hyperbolaa\Plugins\Process;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class Installer
{
    /**
     * The plugin name.
     *
     * @var string
     */
    protected $name;

    /**
     * The version of plugin being installed.
     *
     * @var string
     */
    protected $version;

    /**
     * The console command instance.
     *
     * @var \Illuminate\Console\Command
     */
    protected $console;

    /**
     * The process timeout.
     *
     * @var int
     */
    protected $timeout = 3360;


    /**
     * The constructor.
     *
     * @param string $name
     * @param string $version
     */
    public function __construct($name, $version = null)
    {
        $this->name = $name;
        $this->version = $version;
    }


    /**
     * Set console command instance.
     *
     * @param \Illuminate\Console\Command $console
     *
     * @return $this
     */
    public function setConsole(Command $console)
    {
        $this->console = $console;

        return $this;
    }

    /**
     * Set process timeout.
     *
     * @param int $timeout
     *
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Run the installation process.
     *
     * @return \Symfony\Component\Process\Process
     */
    public function run()
    {
        $process = $this->getProcess();

        $process->setTimeout($this->timeout);

        if ($this->console instanceof Command) {
            $process->run(function ($type, $line) {
                $this->console->line($line);
            });
        }

        return $process;
    }

    /**
     * Get process instance.
     *
     * @return \Symfony\Component\Process\Process
     */
    public function getProcess()
    {
        return $this->installViaComposer();
    }


    /**
     * Get composer package name.
     *
     * @return string
     */
    public function getPackageName()
    {
        if (is_null($this->version)) {
            return $this->name . ':dev-master';
        }

        return $this->name . ':' . $this->version;
    }

    /**
     * Install the plugin via composer.
     *
     * @return \Symfony\Component\Process\Process
     */
    public function installViaComposer()
    {
        return Process::fromShellCommandline(sprintf(
            'cd %s && composer require %s',
            base_path(),
            $this->getPackageName()
        ));
    }
}
