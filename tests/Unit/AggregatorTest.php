<?php

use Borsch\Config\Aggregator;
use Borsch\Config\Config;

covers(Aggregator::class);

beforeEach(function () {
    // Clean up cache file before each test
    $cacheFile = __DIR__ . '/../Config/cache.config.php';
    if (file_exists($cacheFile)) {
        unlink($cacheFile);
    }
});

afterAll(function () {
    // Clean up cache file before each test
    $cacheFile = __DIR__ . '/../Config/cache.config.php';
    if (file_exists($cacheFile)) {
        unlink($cacheFile);
    }
});

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
