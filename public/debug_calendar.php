<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::capture());

use Spatie\GoogleCalendar\Event;
use Carbon\Carbon;

header('Content-Type: text/plain');

try {
    $calId = config('google-calendar.calendar_id');
    echo "Calendar ID: " . $calId . "\n";
    $events = Event::get(); // Default: ab jetzt
    echo "Total Events (ab jetzt): " . count($events) . "\n\n";

    foreach ($events as $event) {
        echo "Name: " . $event->name . "\n";
        echo "ID: " . ($event->id ?? 'no id') . "\n";
        
        $isAllDay = $event->isAllDayEvent();
        echo "Is All Day: " . ($isAllDay ? 'Yes' : 'No') . "\n";
        
        $start = $event->startDateTime ?? $event->startDate;
        $end = $event->endDateTime ?? $event->endDate;
        
        echo "Raw Start: " . ($start ? $start->toIso8601String() : 'null') . "\n";
        echo "Raw End:   " . ($end ? $end->toIso8601String() : 'null') . "\n";
        
        if ($isAllDay) {
            echo "Format Start: " . ($start ? $start->toDateString() : 'null') . "\n";
            echo "Format End:   " . ($end ? $end->toDateString() : 'null') . "\n";
        } else {
            echo "Format Start: " . ($start ? $start->toIso8601String() : 'null') . "\n";
            echo "Format End:   " . ($end ? $end->toIso8601String() : 'null') . "\n";
        }
        echo "-----------------------------------\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
