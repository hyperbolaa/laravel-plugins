<?php

namespace Hyperbolaa\Plugins\Commands;

use Illuminate\Console\Command;

class UnUseCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'plugin:unuse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Forget the used plugin with plugin:use';

    /**
     * Execute the console command.
     */
    public function handle() : int
    {
        $this->laravel['plugins']->forgetUsed();

        $this->info('Previous plugin used successfully forgotten.');

        return 0;
    }
}
