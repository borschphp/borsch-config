<?php

namespace Borsch\Config;

use Borsch\Config\Exception\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Config implements ContainerInterface
{

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(
        /** @var array<string, mixed> */
        private array $config = []
    ) {}

    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw NotFoundException::forEntry($id);
        }

        return $this->config[$id];
    }

    public function has(string $id): bool
    {
        return isset($this->config[$id]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getOrDefault(string $id, mixed $default = null): mixed
    {
        if ($this->has($id)) {
            return $this->get($id);
        }

        return $default;
    }

    public function merge(Config $config): Config
    {
        if ($config !== $this && !empty($config->config)) {
            foreach ($config->config as $key => $value) {
                $this->config[$key] = $value;
            }
        }

        return $this;
    }
}
