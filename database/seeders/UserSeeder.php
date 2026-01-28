<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $auditDeptId = DB::table('departments')->where('code', 'AUD')->value('id');
        $pnaDeptId = DB::table('departments')->where('code', 'PNA')->value('id');

        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'root@auticket.local',
                'password' => Hash::make('password'),
                'department_id' => $auditDeptId,
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kepala Bagian Audit',
                'email' => 'admin.audit@auticket.local',
                'password' => Hash::make('password'),
                'department_id' => $auditDeptId,
                'role' => 'pengawas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kepala Bagian PNA',
                'email' => 'admin.pna@auticket.local',
                'password' => Hash::make('password'),
                'department_id' => $pnaDeptId,
                'role' => 'pengawas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@auticket.local',
                'password' => Hash::make('password'),
                'department_id' => null,
                'role' => 'staff',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sari Wulandari',
                'email' => 'sari@auticket.local',
                'password' => Hash::make('password'),
                'department_id' => null,
                'role' => 'staff',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Anto Wijaya',
                'email' => 'anto@auticket.local',
                'password' => Hash::make('password'),
                'department_id' => null,
                'role' => 'staff',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@auticket.local',
                'password' => Hash::make('password'),
                'department_id' => null,
                'role' => 'staff',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert($user);
        }
    }
}
