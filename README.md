# Borsch Config

A lightweight and easy to use configuration library.

## Features

- Simple and intuitive API
- Support for multiple file formats (JSON, YAML, INI, DOTENV)
- Aggregate multiple configuration sources
- Caching for improved performance in production

## Installation

Via [composer](https://getcomposer.org) :

```bash
composer require borschphp/config
```

## Usage

The `Config` class implements the `ContainerInterface` from PSR-11, therefore you have access to all its methods to
check if a key exists and to retrieve its value.  
A `getOrDefault()` method is also available to provide a default value if the key does not exist.

Internally, readers are used to parse configuration files of different formats. The following readers are available:
- `Borsch\Config\Reader\Json` for JSON files
- `Borsch\Config\Reader\Yaml` for YAML files (via `symfony/yaml`)
- `Borsch\Config\Reader\Ini` for INI files
- `Borsch\Config\Reader\DotEnv` for DOTENV files (via `vlucas/phpdotenv`)

The `Aggregator` class allows you to merge multiple configuration sources into a single `Config` instance. You can also
provide a cache file path to store the merged configuration for faster access in production environments.

A simple example with just one reader :

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Borsch\Config\Config;
use Borsch\Config\Reader\DotEnv;

$env = new DotEnv();
$env_data = $env->fromFile(__DIR__ . '/.env');

$config = new Config($env_data);

$key = $config->has('key') ? $config->get('key') : 'other_value';
$other = $config->getOrDefault('other', 'default_value');
```

A more complete example with multiple readers :

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Borsch\Config\Aggregator;
use Borsch\Config\Config;
use Borsch\Config\Reader\DotEnv;
use Borsch\Config\Reader\Ini;
use Borsch\Config\Reader\Json;
use Borsch\Config\Reader\Yaml;

$ini = new Ini();
$ini_data = $ini->fromFile(__DIR__ . '/config.ini');

$env = new DotEnv();
$env_data = $env->fromFile(__DIR__ . '/.env');

$json = new Json();
$json_data = $json->fromFile(__DIR__ . '/config.json');

$yaml = new Yaml();
$yaml_data = $yaml->fromFile(__DIR__ . '/config.yaml');

$aggregator = new Aggregator(
    [
        $ini_data,
        $env_data,
        $json_data,
        $yaml_data,
        [
            'key' => 'value'
        ]
    ]
);

/** @var Config $config */
$config = $aggregator->getMergedConfig();

$key = $config->has('key') ? $config->get('key') : 'other_value';
$other = $config->getOrDefault('other', 'default_value');
```

## Providers

Providers allow you to specify file paths, internally it uses the `glob()` function to find matching files so you can use
`glob()` patterns.
Because the providers are callable, configuration loading can be deferred until actually needed, this is the recommended
way to build your configuration in order to avoid unnecessary file parsing **in production**.

There is no interface for providers, they just need to be classes implementing the `__invoke()` method and returning an
array.  
Therefore, it is possible to only provide the provider FQDN if there is no constructor arguments. Example :

```php
$aggregator = new Aggregator(
    [
        Application\Config\MyConfigProvider::class
    ]
);
```

A complete example with **providers** and **caching enabled** :

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Borsch\Config\Aggregator;
use Borsch\Config\Config;
use Borsch\Config\Provider\DotEnvProvider;
use Borsch\Config\Provider\IniProvider;
use Borsch\Config\Provider\JsonProvider;
use Borsch\Config\Provider\PhpFileProvider;
use Borsch\Config\Provider\YamlProvider;

$aggregator = new Aggregator(
    [
        new PhpFileProvider('config/production.*.config.php'),
        new JsonProvider('config/production.*.config.json'),
        new YamlProvider('config/production.*.config.y{a,}ml'),
        new IniProvider('config/production.*.config.ini'),
        new DotEnvProvider('config/.env.production.*'),
    ],
    cache_file: __DIR__.'/storage/cache/config.cache.php',
    use_cache: true
);

/** @var Config $config */
$config = $aggregator->getMergedConfig();

$key = $config->has('key') ? $config->get('key') : 'other_value';
$other = $config->getOrDefault('other', 'default_value');
```

## License

The package is licensed under the MIT license.
See [License File](https://github.com/borschphp/borsch-config/blob/master/LICENSE.md) for more information.
