echo "<h3>Current Directory: " . __DIR__ . "</h3>";
$files = scandir(__DIR__);
echo "<pre>";
print_r($files);
echo "</pre>";

if (is_dir(__DIR__ . '/database/migrations')) {
    echo "<h3>Migrations Directory:</h3>";
    $filesMig = scandir(__DIR__ . '/database/migrations');
    echo "<pre>";
    print_r($filesMig);
    echo "</pre>";
}
