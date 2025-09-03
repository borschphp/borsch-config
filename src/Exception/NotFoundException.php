<?php

namespace Borsch\Config\Exception;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends Exception implements NotFoundExceptionInterface
{

    public static function forEntry(string $id): self
    {
        return new self("Config key '$id' not found.");
    }
}
