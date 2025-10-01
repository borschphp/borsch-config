<?php

namespace Borsch\Config\Reader;

use Borsch\Config\Exception\ReaderException;

interface ReaderInterface
{

    /**
     * Read from file and return an array.
     *
     * @return array<string, mixed>
     * @throws ReaderException
     */
    public function fromFile(string $filename): array;

    /**
     * Read from string and return an array.
     *
     * @return array<string, mixed>
     * @throws ReaderException
     */
    public function fromString(string $contents): array;
}
