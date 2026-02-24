<?php
// Safe-Mode Cache Cleaner (Direct File System)
// Location: public/clear-cache.php

echo "<h1>Safe-Mode Cache Cleaner</h1>";

$paths = [
    'Views' => __DIR__.'/../storage/framework/views/',
    'Cache' => __DIR__.'/../storage/framework/cache/',
    'Sessions' => __DIR__.'/../storage/framework/sessions/',
];

foreach ($paths as $name => $path) {
    echo "Processing $name ($path)... ";
    if (!is_dir($path)) {
        echo "Directory not found.<br>";
        continue;
    }

    $files = glob($path . '*');
    $count = 0;
    foreach ($files as $file) {
        if (is_file($file) && basename($file) !== '.gitignore') {
            unlink($file);
            $count++;
        }
    }
    echo "Deleted $count files.<br>";
}

echo "<h2>Fertig! Bitte Dashboard jetzt neu laden.</h2>";
