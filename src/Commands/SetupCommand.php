<?php

namespace Hyperbolaa\Plugins\Commands;

use Illuminate\Console\Command;

class SetupCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setting up plugins folders for first use.';

    /**
     * Execute the console command.
     */
    public function handle() : int
    {
        $code = $this->generatePluginsFolder();

        return $this->generateAssetsFolder() | $code;
    }

    /**
     * Generate the plugins folder.
     */
    public function generatePluginsFolder()
    {
        return $this->generateDirectory(
            $this->laravel['plugins']->config('paths.plugins'),
            'Plugins directory created successfully',
            'Plugins directory already exist'
        );
    }

    /**
     * Generate the assets folder.
     */
    public function generateAssetsFolder()
    {
        return $this->generateDirectory(
            $this->laravel['plugins']->config('paths.assets'),
            'Assets directory created successfully',
            'Assets directory already exist'
        );
    }

    /**
     * Generate the specified directory by given $dir.
     *
     * @param $dir
     * @param $success
     * @param $error
     * @return int
     */
    protected function generateDirectory($dir, $success, $error) : int
    {
        if (!$this->laravel['files']->isDirectory($dir)) {
            $this->laravel['files']->makeDirectory($dir, 0755, true, true);

            $this->info($success);

            return 0;
        }

        $this->error($error);

        return E_ERROR;
    }
}
