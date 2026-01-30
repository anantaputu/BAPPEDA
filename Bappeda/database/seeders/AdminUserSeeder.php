<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@bappeda.go.id'],
            [
                'name'      => 'yesss',
                'username'  => 'admin',
                'password'      => Hash::make('admin123'),
                'role_id'       => 1, 
                'nama_depan'    => 'Admin',
                'nama_belakang' => 'BAPPEDA',
                'status_aktif'  => true,
                'email_verified_at' => now(),
            ]
            

        );
    }
}
