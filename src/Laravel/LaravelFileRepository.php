<?php

namespace Hyperbolaa\Plugins\Laravel;

use Hyperbolaa\Plugins\FileRepository;

class LaravelFileRepository extends FileRepository
{
    /**
     * {@inheritdoc}
     */
    protected function createModule(...$args)
    {
        return new Plugin(...$args);
    }
}
