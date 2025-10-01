<?php

use Borsch\Config\Provider\IniProvider;

@covers(IniProvider::class);

test('loads configuration from .ini files', function () {
    // Create temporary INI config files
    $file1 = tempnam(sys_get_temp_dir(), 'config1_') . '.ini';
    $file2 = tempnam(sys_get_temp_dir(), 'config2_') . '.ini';

    file_put_contents($file1, "key1=value1\nkey2=value2");
    file_put_contents($file2, "key3=value3\nkey4=value4");

    // Use a glob pattern that matches the created files
    $globPattern = sys_get_temp_dir() . '/config*_*.ini';
    $provider = new IniProvider($globPattern);

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
