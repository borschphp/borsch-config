<?php

namespace Borsch\Config\Reader;

interface ReaderInterface
{

    /**
     * Read from file and return an array.
     *
     * @return array<string, mixed>
     */
    public function fromFile(string $filename): array;

    /**
     * Read from string and return an array.
     *
     * @return array<string, mixed>
     */
    public function fromString(string $contents): array;
}
