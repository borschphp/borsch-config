<?php

namespace Borsch\Config\Provider;

use Borsch\Config\Exception\ProviderException;
use Borsch\Config\Exception\ReaderException;
use Borsch\Config\Reader\ReaderInterface;

trait ReaderProviderTrait
{

    private readonly ReaderInterface $reader;

    /**
     * @return array<string, mixed>
     * @throws ReaderException
     * @throws ProviderException
     */
    public function __invoke(): array
    {
        $config = [];

        $files = glob($this->glob_pattern, GLOB_BRACE);
        if ($files === false) {
            throw ProviderException::unableToReadFilesFromPattern($this->glob_pattern);
        }

        foreach ($files as $file) {
            if (is_file($file) && is_readable($file)) {
                $config = array_merge($config, $this->reader->fromFile($file));
            }
        }

        return $config;
    }
}
