<?php

namespace Borsch\Config\Exception;

use Exception;
use function sprintf;

class ReaderException extends Exception
{

    public static function fileDoesNotExistOrIsNotReadable(string $filename): self
    {
        return new self("The file '$filename' does not exist or is not readable.");
    }

    public static function errorReadingFile(string $filename, string $message, string $type = ''): self
    {
        return new self(sprintf(
            'Error reading %sfile "%s": %s',
            $type ? "$type " : '',
            $filename,
            $message
        ));
    }

    public static function errorReadingString(string $message, string $type = ''): self
    {
        return new self(sprintf(
            'Error reading %sstring: %s',
            $type ? "$type " : '',
            $message
        ));
    }

    public static function invalidArgument(string $argument, string $type = ''): self
    {
        return new self(sprintf(
            'Invalid %s for %sreader.',
            $argument,
            $type ? "$type " : ''
        ));
    }

    public static function runtimeError(string $message): self
    {
        return new self($message);
    }
}
