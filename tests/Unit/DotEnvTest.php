<?php

use Borsch\Config\Exception\ReaderException;
use Borsch\Config\Reader\DotEnv;

covers(DotEnv::class);

test('fromFile() loads environment variables from file', function () {
    $dotenv = new DotEnv();
    $data = $dotenv->fromFile(__DIR__ . '/../Config/.env');

    expect($data)->toBeArray()
        ->and($data['APP_URL'])->toBe('http://localhost')
        ->and($data['APP_PORT'])->toBe('8000')
        ->and($data['DB_HOST'])->toBe('localhost')
        ->and($data['DB_PORT'])->toBe('5432');
});

test('fromFile() throws exception when file does not exist', function () {
    $dotenv = new DotEnv();
    $dotenv->fromFile(__DIR__ . '/../Config/.not.env');
})->throws(ReaderException::class, "The file '".__DIR__."/../Config/.not.env' does not exist or is not readable.");

test('fromFile() throws exception when error in file', function () {
    $dotenv = new DotEnv();
    $dotenv->fromFile(__DIR__ . '/../Config/.error.env');
})->throws(ReaderException::class);

test('fromString() loads environment variables from string', function () {
    $dotenv = new DotEnv();
    $data = $dotenv->fromString("FOO=Bar\nBAZ=Buzz");

    expect($data)->toBeArray()
        ->and($data['FOO'])->toBe('Bar')
        ->and($data['BAZ'])->toBe('Buzz');
});

test('fromString() throws exception when error in string', function () {
    $dotenv = new DotEnv();
    $dotenv->fromString("APP_URL:too_long=http://localhost");
})->throws(ReaderException::class);
