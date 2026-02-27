echo "<h3>Current Directory: " . __DIR__ . "</h3>";
$files = scandir(__DIR__);
echo "<pre>";
print_r($files);
echo "</pre>";

if (is_dir(__DIR__ . '/public')) {
    echo "<h3>Public Directory:</h3>";
    $filesPublic = scandir(__DIR__ . '/public');
    echo "<pre>";
    print_r($filesPublic);
    echo "</pre>";
}
