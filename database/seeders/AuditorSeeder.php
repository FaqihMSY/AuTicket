<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuditorSeeder extends Seeder
{
    public function run(): void
    {
        $budiId = DB::table('users')->where('email', 'budi@auticket.local')->value('id');
        $sariId = DB::table('users')->where('email', 'sari@auticket.local')->value('id');
        $antoId = DB::table('users')->where('email', 'anto@auticket.local')->value('id');
        $dewiId = DB::table('users')->where('email', 'dewi@auticket.local')->value('id');

        DB::table('auditors')->insert([
            [
                'user_id' => $budiId,
                'specialization' => 'Keuangan, Operasional',
                'certification' => 'CIA',
                'is_active' => true,
                'performance_score' => 95.00,
                'total_completed_projects' => 12,
                'average_completion_days' => 14.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $sariId,
                'specialization' => 'IT Audit, Compliance',
                'certification' => 'CISA',
                'is_active' => true,
                'performance_score' => 88.00,
                'total_completed_projects' => 10,
                'average_completion_days' => 16.20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $antoId,
                'specialization' => 'Operasional',
                'certification' => null,
                'is_active' => true,
                'performance_score' => 82.00,
                'total_completed_projects' => 8,
                'average_completion_days' => 18.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $dewiId,
                'specialization' => 'Analisa Risiko, Perencanaan',
                'certification' => 'CRM',
                'is_active' => true,
                'performance_score' => 90.00,
                'total_completed_projects' => 15,
                'average_completion_days' => 12.30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
