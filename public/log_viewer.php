<?php
$logFile = __DIR__ . '/../storage/logs/laravel.log';
if (file_exists($logFile)) {
    $content = file($logFile);
    $lastLines = array_slice($content, -20);
    echo "<pre>";
    foreach ($lastLines as $line) {
        if (strpos($line, 'Offers Debug:') !== false) {
            echo htmlspecialchars($line);
        }
    }
    echo "</pre>";
} else {
    echo "Log file not found at: " . $logFile;
}
?>
