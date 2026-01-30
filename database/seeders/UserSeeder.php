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
                'name' => 'Jarot Sembodo',
                'email' => 'jarot.sembodo@sucofindo.co.id',
                'password' => Hash::make('password'),
                'role' => 'reviewer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nanda',
                'email' => 'nanda@sucofindo.co.id',
                'password' => Hash::make('password'),
                'role' => 'pengawas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Munadi',
                'email' => 'munadi@sucofindo.co.id',
                'password' => Hash::make('password'),
                'role' => 'pengawas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Staff Users
            ['name' => 'Bhenyh', 'email' => 'bhenyh@sucofindo.co.id'],
            ['name' => 'Chuswatul', 'email' => 'chuswatul@sucofindo.co.id'],
            ['name' => 'Didin Pahrudin', 'email' => 'didin.pahrudin@sucofindo.co.id'],
            ['name' => 'Farah Fadhilah', 'email' => 'farah.fadhilah@sucofindo.co.id'],
            ['name' => 'Fwidiarso', 'email' => 'fwidiarso@sucofindo.co.id'],
            ['name' => 'H Syafmi', 'email' => 'h.syafmi@sucofindo.co.id'],
            ['name' => 'M Fahmi Illmi', 'email' => 'm.fahmiillmi@sucofindo.co.id'],
            ['name' => 'Marsilia', 'email' => 'marsilia@sucofindo.co.id'],
            ['name' => 'Marsya Heilma', 'email' => 'marsya.heilma@sucofindo.co.id'],
            ['name' => 'Nadia Wismaya', 'email' => 'nadia.wismaya@sucofindo.co.id'],
            ['name' => 'Rio Pradifta', 'email' => 'rio.pradifta@sucofindo.co.id'],
            ['name' => 'Samuel Pinandhito', 'email' => 'samuel.pinandhito@sucofindo.co.id'],
            ['name' => 'Sefira Achmadi', 'email' => 'sefira.achmadi@sucofindo.co.id'],
            ['name' => 'Shafira Novieta', 'email' => 'shafira.novieta@sucofindo.co.id'],
            ['name' => 'Sugengf', 'email' => 'sugengf@sucofindo.co.id'],
            ['name' => 'Wandra', 'email' => 'wandra@sucofindo.co.id'],
        ];

        foreach ($users as $userData) {
            if (!isset($userData['role'])) {
                $userData['role'] = 'staff';
            }
            if (!isset($userData['password'])) {
                $userData['password'] = Hash::make('password');
            }
            $userData['created_at'] = now();
            $userData['updated_at'] = now();

            DB::table('users')->insert($userData);
        }
    }
}
