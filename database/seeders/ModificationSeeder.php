<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        \App\Models\Modification::truncate();

        $data = [
            'Jan' => [
                'Shearing Line' => 2,
                'Furnace' => 3,
                'Finishing Mill' => 5,
                'ACC' => 1,
            ],
            'Feb' => [
                'Shearing Line' => 3,
                'Furnace' => 1,
                'Finishing Mill' => 5,
                'ACC' => 2,
            ],
            'Mar' => [
                'Shearing Line' => 4,
                'Furnace' => 0,
                'Finishing Mill' => 5,
                'ACC' => 0,
            ],
            'Apr' => [
                'Shearing Line' => 3,
                'Furnace' => 3,
                'Finishing Mill' => 11,
                'ACC' => 3,
            ],
            'May' => [
                'Shearing Line' => 0,
                'Furnace' => 0,
                'Finishing Mill' => 0,
                'ACC' => 0,
            ],
        ];

        $months = [
            'Jan' => '01',
            'Feb' => '02',
            'Mar' => '03',
            'Apr' => '04',
            'May' => '05',
        ];

        foreach ($data as $monthName => $teams) {
            foreach ($teams as $team => $count) {
                for ($i = 0; $i < $count; $i++) {
                    \App\Models\Modification::create([
                        'title' => "Modification $team " . ($i + 1) . " ($monthName)",
                        'team' => $team,
                        'description' => "Official request for $team in $monthName",
                        'status' => 'completed',
                        'request_date' => "2026-" . $months[$monthName] . "-" . rand(1, 28),
                    ]);
                }
            }
        }
    }
}
