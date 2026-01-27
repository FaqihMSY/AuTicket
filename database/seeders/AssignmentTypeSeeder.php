<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignmentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $auditDeptId = DB::table('departments')->where('code', 'AUD')->value('id');
        $pnaDeptId = DB::table('departments')->where('code', 'PNA')->value('id');

        DB::table('assignment_types')->insert([
            [
                'department_id' => $auditDeptId,
                'name' => 'Audit Rutin',
                'code' => 'AUD_RUTIN',
                'description' => 'Audit rutin tahunan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department_id' => $auditDeptId,
                'name' => 'Audit Khusus',
                'code' => 'AUD_KHUSUS',
                'description' => 'Audit khusus berdasarkan permintaan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department_id' => $auditDeptId,
                'name' => 'Investigasi',
                'code' => 'AUD_INVESTIGASI',
                'description' => 'Investigasi kasus tertentu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department_id' => $auditDeptId,
                'name' => 'Audit Kepatuhan',
                'code' => 'AUD_KEPATUHAN',
                'description' => 'Audit kepatuhan terhadap regulasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department_id' => $auditDeptId,
                'name' => 'Review Internal',
                'code' => 'AUD_REVIEW',
                'description' => 'Review internal prosedur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department_id' => $pnaDeptId,
                'name' => 'Analisa Risiko',
                'code' => 'PNA_RISIKO',
                'description' => 'Analisa risiko organisasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department_id' => $pnaDeptId,
                'name' => 'Perencanaan Strategis',
                'code' => 'PNA_STRATEGIS',
                'description' => 'Perencanaan strategis jangka panjang',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department_id' => $pnaDeptId,
                'name' => 'Review Kebijakan',
                'code' => 'PNA_KEBIJAKAN',
                'description' => 'Review kebijakan perusahaan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department_id' => $pnaDeptId,
                'name' => 'Studi Kelayakan',
                'code' => 'PNA_KELAYAKAN',
                'description' => 'Studi kelayakan proyek',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department_id' => $pnaDeptId,
                'name' => 'Evaluasi Program',
                'code' => 'PNA_EVALUASI',
                'description' => 'Evaluasi program yang berjalan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
