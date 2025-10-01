<?php

use Borsch\Config\Provider\DotEnvProvider;

@covers(DotEnvProvider::class);

test('loads configuration from .env files', function () {
    // Create temporary PHP config files
    $file1 = tempnam(sys_get_temp_dir(), '.env.');
    $file2 = tempnam(sys_get_temp_dir(), '.env.');

    file_put_contents($file1, "KEY1=value1\nKEY2=value2");
    file_put_contents($file2, "KEY3=value3\nKEY4=value4");

    // Use a glob pattern that matches the created files
    $globPattern = sys_get_temp_dir() . '/.env.*';
    $provider = new DotEnvProvider($globPattern);

    $config = $provider();

    expect($config)->toMatchArray([
        'KEY1' => 'value1',
        'KEY2' => 'value2',
        'KEY3' => 'value3',
        'KEY4' => 'value4',
    ]);

    // Clean up temporary files
    unlink($file1);
    unlink($file2);
});
