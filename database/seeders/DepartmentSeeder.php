<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('departments')->insert([
            [
                'name' => 'Audit',
                'code' => 'AUD',
                'description' => 'Bagian Audit Internal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Perencanaan & Analisa',
                'code' => 'PNA',
                'description' => 'Bagian Perencanaan dan Analisa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
