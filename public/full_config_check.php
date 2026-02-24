<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "<h3>Detailed Config Check</h3>";
echo "Session Driver: " . config('session.driver') . "<br>";
echo "Session Lifetime: " . config('session.lifetime') . "<br>";
echo "Session Cookie Name: " . config('session.cookie') . "<br>";
echo "Session Domain: [" . config('session.domain') . "] (Should be null or 'dev.frankgroup.net')<br>";
echo "Session Secure: " . (config('session.secure') ? 'TRUE' : 'FALSE') . "<br>";
echo "Session HttpOnly: " . (config('session.http_only') ? 'TRUE' : 'FALSE') . "<br>";
echo "Session SameSite: " . config('session.same_site') . "<br>";

echo "<hr>";
echo "FILESYSTEM Check:<br>";
$sessionPath = storage_path('framework/sessions');
echo "Session Path: $sessionPath<br>";
echo "Is Writable: " . (is_writable($sessionPath) ? "YES" : "NO") . "<br>";
echo "Files in session path: " . count(glob($sessionPath . '/*')) . "<br>";

echo "<hr>";
echo "ENV File Content (Sanitized):<pre>";
$env = file_exists(__DIR__ . '/../.env') ? file_get_contents(__DIR__ . '/../.env') : 'NOT FOUND';
echo htmlspecialchars(preg_replace('/PASSWORD=.*/i', 'PASSWORD=********', $env));
echo "</pre>";
?>
EOD;
