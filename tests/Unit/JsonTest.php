<?php

use Borsch\Config\Exception\ReaderException;
use Borsch\Config\Reader\Json;

covers(Json::class);

test('fromFile() loads environment variables from file', function () {
    $json = new Json();
    $data = $json->fromFile(__DIR__ . '/../Config/config.json');

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
    $json = new Json();
    $json->fromFile(__DIR__ . '/../Config/unknow.json');
})->throws(ReaderException::class, "The file '".__DIR__."/../Config/unknow.json' does not exist or is not readable.");

test('fromFile() throws exception when error in file', function () {
    $json = new Json();
    $json->fromFile(__DIR__ . '/../Config/error.json');
})->throws(ReaderException::class);

test('fromString() loads environment variables from string', function () {
    $json = new Json();
    $data = $json->fromString("{\"host\": \"localhost\", \"port\": 8080}");

    expect($data)->toBeArray()
        ->and($data['host'])->toBe('localhost')
        ->and($data['port'])->toBe(8080);
});

test('fromString() throws exception when error in string', function () {
    $json = new Json();
    $json->fromString("{\"\"host\": \"localhost\", \"port\": 8080}");
    // ----------------------------^ note the extra quote here
})->throws(ReaderException::class);
