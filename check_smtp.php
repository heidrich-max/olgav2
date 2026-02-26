<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$project = \App\Models\CompanyProject::where('name', 'Deine Fanartikel')->first();

if (!$project) {
    echo "Projekt nicht gefunden.\n";
    exit;
}

echo "Projekt: " . $project->name . "\n";
echo "SMTP Host: " . ($project->smtp_host ?: 'FEHLT') . "\n";
echo "SMTP User: " . ($project->smtp_user ?: 'FEHLT') . "\n";
echo "SMTP Pass gesetzt: " . ($project->smtp_password ? 'JA (Länge: ' . strlen($project->smtp_password) . ')' : 'NEIN') . "\n";
echo "SMTP Port: " . ($project->smtp_port ?: 'FEHLT') . "\n";
echo "SMTP Encryption: " . ($project->smtp_encryption ?: 'FEHLT') . "\n";
