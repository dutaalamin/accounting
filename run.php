<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$employee = App\Models\Employee::first();
App\Models\Overtime::updateOrCreate(
    ['date' => '2026-05-21', 'reason' => 'CB 6 Jam'],
    [
        'employee_id' => $employee->id,
        'start_time' => '08:00:00',
        'end_time' => '14:00:00',
        'duration_hours' => 6,
        'type' => 'maintenance',
        'status' => 'approved'
    ]
);
App\Models\Overtime::updateOrCreate(
    ['date' => '2026-05-26', 'reason' => 'CB 8 Jam'],
    [
        'employee_id' => $employee->id,
        'start_time' => '08:00:00',
        'end_time' => '16:00:00',
        'duration_hours' => 8,
        'type' => 'maintenance',
        'status' => 'approved'
    ]
);
echo "Done";
