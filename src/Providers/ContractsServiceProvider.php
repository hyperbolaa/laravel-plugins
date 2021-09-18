<?php

namespace Hyperbolaa\Plugins\Providers;

use Illuminate\Support\ServiceProvider;
use Hyperbolaa\Plugins\Contracts\RepositoryInterface;
use Hyperbolaa\Plugins\Laravel\LaravelFileRepository;

class ContractsServiceProvider extends ServiceProvider
{
    /**
     * Register some binding.
     */
    public function register()
    {
        $this->app->bind(RepositoryInterface::class, LaravelFileRepository::class);
    }
}
