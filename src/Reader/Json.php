<?php

namespace Borsch\Config\Reader;

use Borsch\Config\Exception\ReaderException;
use function file_get_contents, is_array, is_file, is_readable, json_last_error, json_last_error_msg;

class Json implements ReaderInterface
{

    /**
     * @return array<string, mixed>
     * @throws ReaderException
     */
    public function fromFile(string $filename): array
    {
        if (!is_file($filename) || !is_readable($filename)) {
            throw ReaderException::fileDoesNotExistOrIsNotReadable($filename);
        }

        $contents = file_get_contents($filename);
        if ($contents === false) {
            throw ReaderException::errorReadingFile($filename, 'Could not read file contents.', 'JSON');
        }

        return $this->decode($contents);
    }

    /**
     * @return array<string, mixed>
     * @throws ReaderException
     */
    public function fromString(string $contents): array
    {
        if (empty($contents)) {
            return [];
        }

        return $this->decode($contents);
    }

    /**
     * @return array<string, mixed>
     * @throws ReaderException
     */
    private function decode(string $json): array
    {
        /** @var array<string, mixed> $data */
        $data = json_decode($json, true);
        if (!is_array($data)) {
            throw ReaderException::runtimeError('Invalid JSON configuration; did not return an array or object.');
        }

        if (json_last_error() === JSON_ERROR_NONE) {
            return $data;
        }

        throw ReaderException::runtimeError(json_last_error_msg());
    }
}
