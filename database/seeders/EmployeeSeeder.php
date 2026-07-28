<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            [
                "name" => "PARK JIN SU",
                "employee_id" => "105764",
                "department" => "IT BUSINESS / PC / PM",
                "position" => "Supervisor",
                "workPeriod" => "-"
            ],
            [
                "name" => "KHAIRUL AKHYAR",
                "employee_id" => "101511",
                "department" => "IT BUSINESS / PC / PM",
                "workPeriod" => "-"
            ],
            [
                "name" => "ADITYO EKO WIBOWO",
                "employee_id" => "106940",
                "department" => "IT BUSINESS / PC / PM",
                "workPeriod" => "-"
            ],
            [
                "name" => "MOCHAMAD SYAWALU RIFA'I",
                "employee_id" => "113355",
                "department" => "IT BUSINESS / PC / PM",
                "workPeriod" => "-"
            ],
            [
                "name" => "MOCHAMAD ANGGA ANGGRIAWAN",
                "employee_id" => "113750",
                "department" => "IT BUSINESS / PC / PM",
                "workPeriod" => "-"
            ],
            [
                "name" => "RAMA ADI WIBOWO",
                "employee_id" => "115391",
                "department" => "IT BUSINESS / PC / PM",
                "workPeriod" => "-"
            ],
            [
                "name" => "DUTA ALAMIN",
                "employee_id" => "115920",
                "department" => "IT BUSINESS",
                "workPeriod" => "2026.01.05 ~ 2026.12.31"
            ],
            [
                "name" => "FADHL AL-HAFIZH",
                "employee_id" => "116054",
                "department" => "IT BUSINESS / PC / PM",
                "workPeriod" => "2026.04.16 ~ 2027.04.30"
            ],

        ];

        foreach ($employees as $emp) {
            Employee::updateOrCreate(
                ['employee_id' => $emp['employee_id']],
                [
                    'name' => $emp['name'],
                    'department' => $emp['department'],
                    'position' => $emp['position'] ?? 'Staff',
                    'notes' => $emp['workPeriod'] !== '-' ? 'Work Period: ' . $emp['workPeriod'] : null,
                    'status' => 'active',
                ]
            );
        }
    }
}
