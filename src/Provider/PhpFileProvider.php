<?php

namespace Borsch\Config\Provider;

use Borsch\Config\Exception\ProviderException;

readonly class PhpFileProvider
{

    public function __construct(
        private string $glob_pattern
    ) {}

    /**
     * @return array<string, mixed>
     * @throws ProviderException
     */
    public function __invoke(): array
    {
        $config = [];

        $files = glob($this->glob_pattern, GLOB_BRACE);
        if ($files === false) {
            throw ProviderException::unableToReadFilesFromPattern($this->glob_pattern);
        }

        // TODO: create a PhpFile reader so that we can use the ReaderProviderTrait here
        foreach ($files as $file) {
            if (is_file($file) && is_readable($file)) {
                /** @var array<string, mixed> $loaded */
                $loaded = require $file;
                if (is_array($loaded)) {
                    $config = array_merge($config, $loaded);
                }
            }
        }

        return $config;
    }
}
