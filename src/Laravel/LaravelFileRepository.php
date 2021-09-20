<?php

namespace Hyperbolaa\Plugins\Laravel;

use Hyperbolaa\Plugins\FileRepository;

class LaravelFileRepository extends FileRepository
{
    /**
     * {@inheritdoc}
     */
    protected function createPlugin(...$args)
    {
        return new Plugin(...$args);
    }
}
