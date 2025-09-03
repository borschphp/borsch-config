<?php

namespace Borsch\Config;

class Aggregator
{

    /** @var array<string, mixed> $conf */
    private array $conf = [];

    /**
     * @param array<array<string, mixed>> $configs
     */
    public function __construct(
        array $configs = [],
        private readonly string $cache_file = '',
        private readonly bool $use_cache = false
    ) {
        if ($this->loadFromCache()) {
            return;
        }

        $this->loadConfigs($configs);
        $this->cacheConfigs();
    }

    public function getMergedConfig(): Config
    {
        return new Config($this->conf);
    }

    /**
     * @param array<array<string, mixed>> $configs
     */
    private function loadConfigs(array $configs): void
    {
        foreach ($configs as $conf) {
            $this->conf = array_merge($this->conf, $conf);
        }
    }

    private function loadFromCache(): bool
    {
        if ($this->use_cache === false || !file_exists($this->cache_file)) {
            return false;
        }

        /** @var array<string, mixed> $loaded */
        $loaded = require $this->cache_file;

        $this->conf = $loaded;

        return true;
    }

    private function cacheConfigs(): void
    {
        if ($this->use_cache === false) {
            return;
        }

        $content = '<?php' . PHP_EOL . PHP_EOL . 'return ' . var_export($this->conf, true) . ';' . PHP_EOL;

        file_put_contents($this->cache_file, $content);
    }
}
