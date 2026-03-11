<?php

use Borsch\Config\Config;
use Borsch\Config\Exception\NotFoundException;
use Borsch\Config\Reader\Ini;
use CuyZ\Valinor\Mapper\MappingError;

covers(Config::class);

test('get() returns the config', function () {
    $init = new Ini();
    $config = new Config($init->fromFile(__DIR__.'/../Config/config.ini'));

    expect($config)->toBeInstanceOf(Config::class)
        ->and($config->get('host'))->toBe('http://localhost:8080')
        ->and($config->get('timeout'))->toBe(30)
        ->and($config->get('use_ssl'))->toBeTrue()
        ->and($config->get('log_level'))->toBe('INFO')
        ->and($config->get('database'))->toBe([
            'username' => 'admin',
            'password' => 'admin',
            'database' => 'mydb',
        ]);
});

test('get() throws exception when entry does not exists', function () {
    $init = new Ini();
    $config = new Config($init->fromFile(__DIR__.'/../Config/config.ini'));

    $config->get('not_exist');
})->throws(NotFoundException::class, "Config key 'not_exist' not found.");

test('has() returns weither an entrey exist or not', function () {
    $init = new Ini();
    $config = new Config($init->fromFile(__DIR__.'/../Config/config.ini'));

    expect($config->has('host'))->toBeTrue()
        ->and($config->has('not_exist'))->toBeFalse();
});

test('getOrDefault() returns the config or the default value', function () {
    $init = new Ini();
    $config = new Config($init->fromFile(__DIR__.'/../Config/config.ini'));

    expect($config->getOrDefault('host'))->toBe('http://localhost:8080')
        ->and($config->getOrDefault('not_exist', 'default_value'))->toBe('default_value')
        ->and($config->getOrDefault('not_exist'))->toBeNull();
});

test('merge() merges two configs', function () {
    $init = new Ini();
    $config1 = new Config($init->fromFile(__DIR__.'/../Config/config.ini'));
    $config2 = new Config([
        'new_key' => 'new_value',
        'host' => 'http://example.com',
    ]);

    $config1->merge($config2);

    expect($config1->get('host'))->toBe('http://example.com')
        ->and($config1->get('new_key'))->toBe('new_value');
});

test('from() returns a new Config instance', function () {
    $init = new Ini();
    $config = new Config($init->fromFile(__DIR__.'/../Config/config.ini'));

    $dbConfig = $config->from('database');

    expect($dbConfig)->toBeInstanceOf(Config::class)
        ->and($dbConfig)->not()->toBe($config)
        ->and($dbConfig->get('username'))->toBe('admin')
        ->and($dbConfig->get('password'))->toBe('admin')
        ->and($dbConfig->get('database'))->toBe('mydb');
});

test('from() throws exception when entry does not exists', function () {
    $init = new Ini();
    $config = new Config($init->fromFile(__DIR__.'/../Config/config.ini'));

    $config->from('not_exist');
})->throws(NotFoundException::class, "Config key 'not_exist' not found.");

// bind()

class DatabaseConfiguration
{
    public string $username;
    public string $password;
    private string $database;

    public function setDatabase(string $database): void
    {
        $this->database = $database;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }
}

test('bind() maps a configuration array into an object', function () {
    $init = new Ini();
    $config = new Config($init->fromFile(__DIR__.'/../Config/config.ini'));

    $db = $config->bind('database', DatabaseConfiguration::class);

    expect($db)->toBeInstanceOf(DatabaseConfiguration::class)
        ->and($db->username)->toBe('admin')
        ->and($db->password)->toBe('admin')
        ->and($db->getDatabase())->toBe('mydb');
});

test('bind() throws exception when entry does not exist', function () {
    $init = new Ini();
    $config = new Config($init->fromFile(__DIR__.'/../Config/config.ini'));

    $config->bind('not_exist', DatabaseConfiguration::class);
})->throws(NotFoundException::class, "Config key 'not_exist' not found.");

test('bind() throws MappingError when values do not match the target class', function () {
    $init = new Ini();
    $config = new Config($init->fromFile(__DIR__.'/../Config/config.ini'));

    // 'host' is a scalar string, not an array matching DatabaseConfiguration
    $config->bind('host', DatabaseConfiguration::class);
})->throws(\CuyZ\Valinor\Mapper\TypeTreeMapperError::class);


