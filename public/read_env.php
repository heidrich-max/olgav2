<?php
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    echo "<h3>Full .env Content:</h3><pre>";
    echo htmlspecialchars(file_get_contents($envPath));
    echo "</pre>";
} else {
    echo ".env not found at $envPath";
}
?>
EOD;
