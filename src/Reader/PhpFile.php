<?php

namespace Borsch\Config\Reader;

use Borsch\Config\Exception\ReaderException;

class PhpFile implements ReaderInterface
{

    public function fromFile(string $filename): array
    {
        if (!is_file($filename) || !is_readable($filename)) {
            throw ReaderException::fileDoesNotExistOrIsNotReadable($filename);
        }

        /** @var array<string, mixed> $data */
        $data = require $filename;
        if (!is_array($data)) {
            throw ReaderException::errorReadingFile($filename, 'Could not read file contents.', 'PHP');
        }

        return $data;
    }

    public function fromString(string $contents): array
    {
        throw ReaderException::runtimeError('Reading PHP configuration from string is not supported.');
    }
}