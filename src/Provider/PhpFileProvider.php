<?php

namespace Borsch\Config\Provider;

readonly class PhpFileProvider
{

    public function __construct(
        private string $glob_pattern
    ) {}

    public function __invoke(): array
    {
        $config = [];

        foreach (glob($this->glob_pattern, GLOB_BRACE) as $file) {
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
