<?php

namespace Hyperbolaa\Plugins\Exceptions;

class InvalidAssetPath extends \Exception
{
    public static function missingModuleName($asset)
    {
        return new static("Plugin name was not specified in asset [$asset].");
    }
}
