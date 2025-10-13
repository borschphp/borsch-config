<?php

namespace Borsch\Config\Attribute;

use Attribute;
use Borsch\Config\Config;
use League\Container\Attribute\AttributeInterface;
use League\Container\{ContainerAwareInterface, ContainerAwareTrait};

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class ConfigSection implements AttributeInterface, ContainerAwareInterface
{

    use ContainerAwareTrait;

    public function __construct(
        private string $section
    ) {}

    public function resolve(): mixed
    {
        return $this->getContainer()->get(Config::class)->from($this->section);
    }
}
