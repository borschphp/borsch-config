<?php

use Borsch\Config\Exception\ReaderException;
use Borsch\Config\Reader\Yaml;

covers(Yaml::class);

test('fromFile() loads environment variables from .env file', function () {
    $yaml = new Yaml();
    $data = $yaml->fromFile(__DIR__ . '/../Config/config.yml');

    expect($data)->toBeArray()
        ->and($data['host'])->toBe('localhost')
        ->and($data['port'])->toBe(8080)
        ->and($data['useSSL'])->toBe(false)
        ->and($data['database'])->toBe([
            'type' => 'mysql',
            'username' => 'root',
            'password' => 'password',
            'name' => 'test_db'
        ]);
});

test('fromFile() throws exception when file does not exist', function () {
    $yaml = new Yaml();
    $yaml->fromFile(__DIR__ . '/../Config/unknow.yml');
})->throws(ReaderException::class, "The file '" . __DIR__ . "/../Config/unknow.yml' does not exist or is not readable.");

test('fromFile() throws exception when error in file', function () {
    $yaml = new Yaml();
    $yaml->fromFile(__DIR__ . '/../Config/error.yml');
})->throws(ReaderException::class);

test('fromString() loads environment variables from env string', function () {
    $yaml = new Yaml();
    $data = $yaml->fromString("host: localhost\nport: 8080");

    expect($data)->toBeArray()
        ->and($data['host'])->toBe('localhost')
        ->and($data['port'])->toBe(8080);
});

test('fromString() throws exception when error in string', function () {
    $yaml = new Yaml();
    $yaml->fromString("host: localhost\n- generate an error by adding invalid characters\nport: 8080");
})->throws(ReaderException::class);
