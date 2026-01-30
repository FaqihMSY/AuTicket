<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AuditorSeeder extends Seeder
{
    public function run(): void
    {
        $staffUsers = User::where('role', 'staff')->get();

        foreach ($staffUsers as $user) {
            DB::table('auditors')->insert([
                'user_id' => $user->id,
                'specialization' => $this->getRandomSpecialization(),
                'is_active' => true,
                'performance_score' => rand(75, 98),
                'total_completed_projects' => rand(0, 20),
                'average_completion_days' => rand(5, 25),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function getRandomSpecialization(): string
    {
        $specializations = [
            'Keuangan, Operasional',
            'IT Audit, Compliance',
            'Operasional',
            'Analisa Risiko, Perencanaan',
            'Manajemen Kualitas',
            'Sistem Informasi',
            'Legal & Kepatuhan'
        ];
        return $specializations[array_rand($specializations)];
    }
}
