<?php
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Start session manually if not handled
if (!$request->hasSession()) {
    $request->setLaravelSession($app['session']->driver());
    $request->session()->start();
}

$active = Session::get('active_company_id', 'NOT SET');
$driver = Config::get('session.driver');

echo "<h3>Session Debug</h3>";
echo "Active Company ID in Session: " . $active . "<br>";
echo "Session Driver: " . $driver . "<br>";

if (isset($_GET['set'])) {
    Session::put('active_company_id', $_GET['set']);
    Session::save();
    echo "Set active_company_id to: " . $_GET['set'] . " (Try reloading without 'set' param)<br>";
}

echo "<hr>";
echo "All Session Data:<pre>" . print_r(Session::all(), true) . "</pre>";
?>