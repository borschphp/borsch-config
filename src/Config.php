<?php

namespace Borsch\Config;

use Borsch\Config\Exception\NotFoundException;
use Psr\Container\{ContainerExceptionInterface, ContainerInterface, NotFoundExceptionInterface};
use Dflydev\DotAccessData\Data;

class Config implements ContainerInterface
{

    private Data $config;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        $this->config = new Data($config);
    }

    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw NotFoundException::forEntry($id);
        }

        return $this->config->get($id);
    }

    public function has(string $id): bool
    {
        return $this->config->has($id);
    }

    public function getOrDefault(string $id, mixed $default = null): mixed
    {
        return $this->config->get($id, $default);
    }

    public function merge(Config $config): Config
    {
        if ($config !== $this && !empty($config->config)) {
            $this->config->importData($config->config);
        }

        return $this;
    }

    /**
     * Create a new Config instance from an existing configuration entry.
     *
     * @param string $id
     * @return Config
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function from(string $id): Config
    {
        $config = $this->get($id);
        if (!is_array($config)) {
            $config = [$config];
        }

        /** @var array<string, mixed> $config */
        return new Config($config);
    }
}
