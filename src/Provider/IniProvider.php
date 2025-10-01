<?php

namespace Borsch\Config\Provider;

use Borsch\Config\Reader\Ini;

readonly class IniProvider
{

    use ReaderProviderTrait;

    public function __construct(
        private string $glob_pattern
    ) {
        $this->reader = new Ini();
    }
}
