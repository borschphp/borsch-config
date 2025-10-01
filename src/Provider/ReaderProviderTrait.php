<?php

namespace Borsch\Config\Provider;

use Borsch\Config\Exception\ReaderException;
use Borsch\Config\Reader\ReaderInterface;

trait ReaderProviderTrait
{

    private readonly ReaderInterface $reader;

    /**
     * @throws ReaderException
     */
    public function __invoke(): array
    {
        $config = [];

        foreach (glob($this->glob_pattern, GLOB_BRACE) as $file) {
            if (is_file($file) && is_readable($file)) {
                $config = array_merge($config, $this->reader->fromFile($file));
            }
        }

        return $config;
    }
}
