<?php

namespace Borsch\Config\Provider;

use Borsch\Config\Reader\Json;

readonly class JsonProvider
{

    use ReaderProviderTrait;

    public function __construct(
        private string $glob_pattern
    ) {
        $this->reader = new Json();
    }
}
