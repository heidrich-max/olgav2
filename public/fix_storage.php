<?php
$base = __DIR__ . '/..';

$dirs = [
    $base . '/storage',
    $base . '/storage/framework',
    $base . '/storage/framework/sessions',
    $base . '/storage/framework/views',
    $base . '/storage/framework/cache',
    $base . '/storage/logs',
    $base . '/bootstrap/cache',
];

echo "<h3>Directory Status:</h3>";
echo "<ul>";
foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        if (mkdir($dir, 0775, true)) {
            echo "<li>Created: $dir</li>";
        } else {
            echo "<li><b style='color:red'>Failed to create:</b> $dir</li>";
        }
    } else {
        echo "<li>Exists: $dir (Writable: " . (is_writable($dir) ? "YES" : "NO") . ")</li>";
        if (!is_writable($dir)) {
            if (chmod($dir, 0775)) {
                echo " - Fixed permissions (0775)";
            } else {
                echo " - <b style='color:red'>Failed to fix permissions</b>";
            }
        }
    }
}
echo "</ul>";

// Check .env file
$envFile = $base . '/.env';
echo "<h3>.env Status:</h3>";
if (file_exists($envFile)) {
    echo "Exists. Writable: " . (is_writable($envFile) ? "YES" : "NO") . "<br>";
    $content = file_get_contents($envFile);
    if (preg_match('/SESSION_DRIVER=(.*)/', $content, $matches)) {
        echo "Current Session Driver: " . $matches[1] . "<br>";
    } else {
        echo "SESSION_DRIVER not found in .env<br>";
    }
} else {
    echo "<b style='color:red'>.env file MISSING!</b> at $envFile<br>";
}
?>
EOD;
