<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'root@auticket.local',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kepala Bagian Audit',
                'email' => 'admin.audit@auticket.local',
                'password' => Hash::make('password'),
                'role' => 'pengawas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kepala Bagian PNA',
                'email' => 'admin.pna@auticket.local',
                'password' => Hash::make('password'),
                'role' => 'pengawas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@auticket.local',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sari Wulandari',
                'email' => 'sari@auticket.local',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Anto Wijaya',
                'email' => 'anto@auticket.local',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@auticket.local',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Reviewer Audit',
                'email' => 'reviewer@auticket.local',
                'password' => Hash::make('password'),
                'role' => 'reviewer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert($user);
        }
    }
}
