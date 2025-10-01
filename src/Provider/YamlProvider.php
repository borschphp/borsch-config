<?php

namespace Borsch\Config\Provider;

use Borsch\Config\Reader\Yaml;

readonly class YamlProvider
{

    use ReaderProviderTrait;

    public function __construct(
        private string $glob_pattern
    ) {
        $this->reader = new Yaml();
    }
}
