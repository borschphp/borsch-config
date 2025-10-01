<?php

use Borsch\Config\Provider\JsonProvider;

@covers(JsonProvider::class);

test('loads configuration from .json files', function () {
    // Create temporary JSON config files
    $file1 = tempnam(sys_get_temp_dir(), 'config1_') . '.json';
    $file2 = tempnam(sys_get_temp_dir(), 'config2_') . '.json';

    file_put_contents($file1, json_encode(['key1' => 'value1', 'key2' => 'value2']));
    file_put_contents($file2, json_encode(['key3' => 'value3', 'key4' => 'value4']));

    // Use a glob pattern that matches the created files
    $globPattern = sys_get_temp_dir() . '/config*.json';
    $provider = new JsonProvider($globPattern);

    $config = $provider();

    expect($config)->toMatchArray([
        'key1' => 'value1',
        'key2' => 'value2',
        'key3' => 'value3',
        'key4' => 'value4',
    ]);

    // Clean up temporary files
    unlink($file1);
    unlink($file2);
});
