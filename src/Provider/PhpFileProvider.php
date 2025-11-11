<?php

namespace Borsch\Config\Provider;

use Borsch\Config\Reader\PhpFile;

readonly class PhpFileProvider
{

    use ReaderProviderTrait;

    public function __construct(
        private string $glob_pattern
    ) {
        $this->reader = new PhpFile();
    }
}
