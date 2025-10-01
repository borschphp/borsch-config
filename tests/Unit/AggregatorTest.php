<?php

use Borsch\Config\Aggregator;
use Borsch\Config\Config;
use Borsch\Config\Exception\AggregatorException;
use Tests\Config\MyConfigProvider;

covers(Aggregator::class);

function test_remove_cache_files(): void
{
    $cacheFile = __DIR__ . '/../Config/cache.config.php';
    file_exists($cacheFile) && unlink($cacheFile);
}

// Clean up cache file before and after each test
beforeEach(fn() => test_remove_cache_files());
afterAll(fn() => test_remove_cache_files());

test('getMergedConfig() returns merged configuration from multiple sources', function () {
    $iniData = ['APP_URL' => 'http://localhost', 'DB_HOST' => 'localhost',];
    $envData = ['APP_PORT' => '8000', 'DB_PORT' => '5432',];

    $aggregator = new Aggregator([$iniData, $envData]);
    $config = $aggregator->getMergedConfig();

    expect($config)->toBeInstanceOf(Config::class)
        ->and($config->get('APP_URL'))->toBe('http://localhost')
        ->and($config->get('APP_PORT'))->toBe('8000')
        ->and($config->get('DB_HOST'))->toBe('localhost')
        ->and($config->get('DB_PORT'))->toBe('5432');
});

test('getMergedConfig() returns merged configuration from provider classes FQDN', function () {
    $aggregator = new Aggregator([
        MyConfigProvider::class
    ]);
    $config = $aggregator->getMergedConfig();

    expect($config)->toBeInstanceOf(Config::class)
        ->and($config->get('MCP-key'))->toBe('value from MyConfigProvider');
});

test('getMergedConfig() returns merged configuration from instantiated provider classes', function () {
    $aggregator = new Aggregator([
        new MyConfigProvider()
    ]);
    $config = $aggregator->getMergedConfig();

    expect($config)->toBeInstanceOf(Config::class)
        ->and($config->get('MCP-key'))->toBe('value from MyConfigProvider');
});

test('getMergedConfig() throws exception on invalid provider', function () {
    $aggregator = new Aggregator([
        'NonExistentClass',
    ]);
    $aggregator->getMergedConfig();
})->throws(
    AggregatorException::class,
    'Invalid config provider of type "string", must be array, invokable object or class name.'
);

test('getMergedConfig() creates cache file', function () {
    $iniData = ['APP_URL' => 'http://localhost', 'DB_HOST' => 'localhost',];
    $envData = ['APP_PORT' => '8000', 'DB_PORT' => '5432',];

    $cacheFile = __DIR__ . '/../Config/cache.config.php';

    $aggregator = new Aggregator(
        [$iniData, $envData],
        $cacheFile,
        true
    );

    $aggregator->getMergedConfig();

    expect($cacheFile)->toBeFile();

    $config = require $cacheFile;

    expect($config)->toBeArray()
        ->and($config['APP_URL'])->toBe('http://localhost')
        ->and($config['APP_PORT'])->toBe('8000')
        ->and($config['DB_HOST'])->toBe('localhost')
        ->and($config['DB_PORT'])->toBe('5432');
});

test('loadFromCache() loads configuration from cache file', function () {
    $cacheFile = __DIR__ . '/../Config/cache.persistent.config.php';
    $aggregator = new Aggregator([], $cacheFile, true);
    $config = $aggregator->getMergedConfig();

    expect($config)->toBeInstanceOf(Config::class)
        ->and($config->get('key'))->toBe('value')
        ->and($config->get('bool'))->toBeTrue();
});
