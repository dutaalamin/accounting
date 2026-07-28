<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

App\Models\Overtime::where('type', 'maintenance')->delete();

$employee = App\Models\Employee::first();
App\Models\Overtime::create([
    'employee_id' => $employee->id,
    'date' => '2026-05-21',
    'start_time' => '07:30:00',
    'end_time' => '13:30:00',
    'duration_hours' => 6,
    'type' => 'maintenance',
    'reason' => 'CB',
    'status' => 'approved'
]);
App\Models\Overtime::create([
    'employee_id' => $employee->id,
    'date' => '2026-05-26',
    'start_time' => '07:30:00',
    'end_time' => '15:30:00',
    'duration_hours' => 8,
    'type' => 'maintenance',
    'reason' => 'CB',
    'status' => 'approved'
]);
echo "Cleaned and added.\n";
