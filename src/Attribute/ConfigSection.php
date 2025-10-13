<?php

namespace Borsch\Config\Attribute;

use Attribute;
use Borsch\Config\Config;
use League\Container\Attribute\AttributeInterface;
use League\Container\{ContainerAwareInterface, ContainerAwareTrait};
use Psr\Container\{ContainerExceptionInterface, NotFoundExceptionInterface};

#[Attribute(Attribute::TARGET_PARAMETER)]
class ConfigSection implements AttributeInterface, ContainerAwareInterface
{

    use ContainerAwareTrait;

    public function __construct(
        private string $section
    ) {}

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function resolve(): mixed
    {
        /** @var Config $config */
        $config = $this->getContainer()->get(Config::class);

        return $config->from($this->section);
    }
}
