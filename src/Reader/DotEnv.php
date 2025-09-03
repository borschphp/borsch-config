<?php

namespace Borsch\Config\Reader;

use Borsch\Config\Exception\ReaderException;
use Dotenv\Dotenv as DotenvParser;
use Exception;

class DotEnv implements ReaderInterface
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

        $directory = dirname($filename);
        $file = basename($filename);

        try {
            $dotenv = DotenvParser::createArrayBacked($directory, $file);

            return $dotenv->load();
        } catch (Exception $e) {
            throw ReaderException::runtimeError($e->getMessage());
        }
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

        try {
            return DotenvParser::parse($contents);
        } catch (Exception $e) {
            throw ReaderException::runtimeError($e->getMessage());
        }
    }
}
