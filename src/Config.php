<?php

namespace Borsch\Config;

use Borsch\Config\Exception\NotFoundException;
use Psr\Container\ContainerInterface;
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
}
