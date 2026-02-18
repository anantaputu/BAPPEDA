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
            ['email' => 'lepang@gmail.com'],
            [
                'name'      => 'M. Khalid Al Rejeki',
                'username'  => 'Khalid',
                'password'      => Hash::make('lepang123'),
                'role_id'       => 1, 
                'nama_depan'    => 'M. Khalid',
                'nama_belakang' => 'Al Rejeki',
                'status_aktif'  => true,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'nanta@gmail.com'],
            [
                'name'      => 'I Putu Ananta Sugiartha',
                'username'  => 'Ananta',
                'password'      => Hash::make('ananta'),
                'role_id'       => 2, 
                'nama_depan'    => 'I Putu Ananta',
                'nama_belakang' => 'Sugiartha',
                'status_aktif'  => true,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'antak@gmail.com'],
            [
                'name'      => 'antak',
                'username'  => 'antak',
                'password'      => Hash::make('ananta'),
                'role_id'       => 2, 
                'nama_depan'    => 'antak',
                'nama_belakang' => 'antak',
                'status_aktif'  => true,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => '1@2.3'],
            [
                'name'      => 'I Putu Ananta Sugiartha',
                'username'  => 'nanto',
                'password'      => Hash::make('123'),
                'role_id'       => 2, 
                'nama_depan'    => 'I Putu Ananta',
                'nama_belakang' => 'Sugiartha',
                'status_aktif'  => true,
                'email_verified_at' => now(),
            ]
        );


    }
}
