<?php

namespace Borsch\Config\Provider;

use Borsch\Config\Reader\DotEnv;

readonly class DotEnvProvider
{

    use ReaderProviderTrait;

    public function __construct(
        private string $glob_pattern
    ) {
        $this->reader = new DotEnv();
    }
}
