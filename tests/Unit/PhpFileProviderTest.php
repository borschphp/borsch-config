<?php

use Borsch\Config\Provider\PhpFileProvider;

@covers(PhpFileProvider::class);

test('loads configuration from PHP files matching a glob pattern', function () {
    // Create temporary PHP config files
    $file1 = tempnam(sys_get_temp_dir(), 'config1_') . '.php';
    $file2 = tempnam(sys_get_temp_dir(), 'config2_') . '.php';

    file_put_contents($file1, '<?php return ["key1" => "value1", "key2" => "value2"];');
    file_put_contents($file2, '<?php return ["key3" => "value3", "key4" => "value4"];');

    // Use a glob pattern that matches the created files
    $globPattern = sys_get_temp_dir() . '/config*_*.php';
    $provider = new PhpFileProvider($globPattern);

    $config = $provider();

    expect($config)->toBe([
        'key1' => 'value1',
        'key2' => 'value2',
        'key3' => 'value3',
        'key4' => 'value4',
    ]);

    // Clean up temporary files
    unlink($file1);
    unlink($file2);
});
