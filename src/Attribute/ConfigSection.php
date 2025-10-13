<?php

namespace Borsch\Config\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY|Attribute::TARGET_PARAMETER)]
readonly class ConfigSection
{

    public function __construct(
        public string $section = ''
    ) {}
}
