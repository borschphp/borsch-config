<?php

namespace Borsch\Config\Exception;

use Exception;

class AggregatorException extends Exception
{

    public static function invalidConfigProvider(mixed $provider): self
    {
        $type = gettype($provider);
        if ($type === 'object') {
            $type = get_class($provider);
        }

        return new self("Invalid config provider of type \"{$type}\", must be array, invokable object or class name.");
    }
}
