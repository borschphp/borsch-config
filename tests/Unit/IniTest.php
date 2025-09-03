<?php

use Borsch\Config\Exception\ReaderException;
use Borsch\Config\Reader\Ini;

covers(Ini::class);

test('fromFile() loads environment variables from file', function () {
    $ini = new Ini();
    $data = $ini->fromFile(__DIR__ . '/../Config/config.ini');

    expect($data['host'])->toBe('http://localhost:8080')
        ->and($data['timeout'])->toBe(30)
        ->and($data['database'])->toBe([
            'username' => 'admin',
            'password' => 'admin',
            'database' => 'mydb',
        ]);
});

test('fromFile() throws exception when file does not exist', function () {
    $ini = new Ini();
    $ini->fromFile(__DIR__ . '/../Config/unknown.config.ini');
})->throws(ReaderException::class, "The file '".__DIR__."/../Config/unknown.config.ini' does not exist or is not readable.");

test('fromFile() throws exception when error in file', function () {
    $ini = new Ini();
    $ini->fromFile(__DIR__ . '/../Config/error.config.ini');
})->throws(ReaderException::class);

test('fromString() loads environment variables from string', function () {
    $ini = new Ini();
    $data = $ini->fromString("host=\"http://localhost:8080\"\ntimeout=30");

    expect($data)->toBeArray()
        ->and($data['host'])->toBe('http://localhost:8080')
        ->and($data['timeout'])->toBe(30);
});

test('fromString() throws exception when error in string', function () {
    $ini = new Ini();
    $ini->fromString("host=\"http://localhost:8080\"\ntimeout=30=60=120");
})->throws(ReaderException::class);
