<?php

namespace Nwidart\Modules\Exceptions;

use Exception;

class InvalidAssetPath extends Exception
{
    public static function missingModuleName($asset)
    {
        return new static("Module name was not specified in asset [$asset].");
    }
}
