<?php

namespace Borsch\Config\Exception;

use Exception;

class ProviderException extends Exception
{

    public static function unableToReadFilesFromPattern(string $pattern): self
    {
        return new self("Unable to read files from pattern: \"$pattern\".");
    }
}
