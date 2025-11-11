<?php

use Borsch\Config\Exception\ReaderException;
use Borsch\Config\Reader\PhpFile;

covers(PhpFile::class);

test('fromFile() loads configuration from PHP file', function () {
    $phpFile = new PhpFile();
    $data = $phpFile->fromFile(__DIR__ . '/../Config/config.php');

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
    $phpFile = new PhpFile();
    $phpFile->fromFile(__DIR__ . '/../Config/unknown.php');
})->throws(ReaderException::class, "The file '".__DIR__."/../Config/unknown.php' does not exist or is not readable.");

test('fromString() throws exception', function () {
    $phpFile = new PhpFile();
    $phpFile->fromString("<?php return ['key' => 'value'];");
})->throws(ReaderException::class, "Reading PHP configuration from string is not supported.");
