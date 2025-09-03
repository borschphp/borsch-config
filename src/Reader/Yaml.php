<?php

namespace Borsch\Config\Reader;

use Borsch\Config\Exception\ReaderException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml as YamlParser;

class Yaml implements ReaderInterface
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

        try {
            /** @var array<string, mixed> */
            return YamlParser::parseFile($filename);
        } catch (ParseException $e) {
            throw ReaderException::errorReadingFile($filename, $e->getMessage(), 'YAML');
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
            /** @var array<string, mixed> */
            return YamlParser::parse($contents);
        } catch (ParseException $e) {
            throw ReaderException::errorReadingString($e->getMessage(), 'YAML');
        }
    }
}
