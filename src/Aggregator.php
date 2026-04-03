<?php

namespace Borsch\Config;

use Borsch\Config\Exception\AggregatorException;

class Aggregator
{

    /** @var array<string, mixed> $conf */
    private array $conf = [];

    /**
     * @param array<array<string, mixed>|class-string|object> $configs
     * @throws AggregatorException
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
     * @param array<array<string, mixed>|class-string|object> $configs
     * @throws AggregatorException
     */
    private function loadConfigs(array $configs): void
    {
        foreach ($configs as $provider) {
            $config = match (true) {
                is_array($provider) => $provider,
                is_string($provider) && class_exists($provider) => (function() use ($provider) {
                    $instance = new $provider();
                    if (!method_exists($instance, '__invoke')) {
                        throw AggregatorException::invalidConfigProvider($provider);
                    }
                    return $instance();
                })(),
                is_object($provider) && method_exists($provider, '__invoke') => ($provider)(),
                default => throw AggregatorException::invalidConfigProvider($provider)
            };

            /**
             * @var array<string, mixed> $config
             * @var array<string, mixed> $merged
             */
            $merged = $this->mergeConfig($this->conf, $config);

            $this->conf = $merged;
        }
    }

    /**
     * Merges two config arrays:
     * - String keys are merged recursively (deep merge).
     * - Integer keys are overwritten (value from $config2 wins).
     *
     * @param array<string, mixed> $config1
     * @param array<string, mixed> $config2
     * @return array<string, mixed>
     */
    private function mergeConfig(array $config1, array $config2): array
    {
        $merged = $config1;

        foreach ($config2 as $key => $value) {
            if (is_string($key) && isset($merged[$key]) && is_array($merged[$key]) && is_array($value)) {
                $merged[$key] = $this->mergeConfig($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
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

    /**
     * @return void
     * @pest-mutate-ignore
     */
    private function cacheConfigs(): void
    {
        if ($this->use_cache === false) {
            return;
        }

        $content = '<?php' . PHP_EOL . PHP_EOL . 'return ' . var_export($this->conf, true) . ';' . PHP_EOL;

        file_put_contents($this->cache_file, $content);
    }
}
