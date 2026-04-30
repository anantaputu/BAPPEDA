<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'M. Khalid Al Rejeki',
                'username' => 'khalid',
                'email' => 'khalid@bappeda.test',
                'password' => Hash::make('password123'),
                'role_id' => 1,
                'nama_depan' => 'M. Khalid',
                'nama_belakang' => 'Al Rejeki',
                'status_aktif' => true,
            ],
            [
                'name' => 'I Putu Ananta Sugiartha',
                'username' => 'ananta',
                'email' => 'ananta@bappeda.test',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'nama_depan' => 'I Putu Ananta',
                'nama_belakang' => 'Sugiartha',
                'status_aktif' => true,
            ],
            [
                'name' => 'Ni Made Ayu Pradnyani',
                'username' => 'ayu.pradnyani',
                'email' => 'ayu.pradnyani@bappeda.test',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'nama_depan' => 'Ni Made Ayu',
                'nama_belakang' => 'Pradnyani',
                'status_aktif' => true,
            ],
            [
                'name' => 'I Wayan Ardika Putra',
                'username' => 'ardika.putra',
                'email' => 'ardika.putra@bappeda.test',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'nama_depan' => 'I Wayan Ardika',
                'nama_belakang' => 'Putra',
                'status_aktif' => true,
            ],
            [
                'name' => 'Kadek Rina Maheswari',
                'username' => 'rina.maheswari',
                'email' => 'rina.maheswari@bappeda.test',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'nama_depan' => 'Kadek Rina',
                'nama_belakang' => 'Maheswari',
                'status_aktif' => true,
            ],
            [
                'name' => 'Komang Gede Saputra',
                'username' => 'gede.saputra',
                'email' => 'gede.saputra@bappeda.test',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'nama_depan' => 'Komang Gede',
                'nama_belakang' => 'Saputra',
                'status_aktif' => true,
            ],
            [
                'name' => 'Luh Putu Sari Dewi',
                'username' => 'sari.dewi',
                'email' => 'sari.dewi@bappeda.test',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'nama_depan' => 'Luh Putu Sari',
                'nama_belakang' => 'Dewi',
                'status_aktif' => true,
            ],
            [
                'name' => 'I Nyoman Wiratmaja',
                'username' => 'nyoman.wiratmaja',
                'email' => 'nyoman.wiratmaja@bappeda.test',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'nama_depan' => 'I Nyoman',
                'nama_belakang' => 'Wiratmaja',
                'status_aktif' => true,
            ],
            [
                'name' => 'Desak Made Paramita',
                'username' => 'desak.paramita',
                'email' => 'desak.paramita@bappeda.test',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'nama_depan' => 'Desak Made',
                'nama_belakang' => 'Paramita',
                'status_aktif' => true,
            ],
            [
                'name' => 'Gusti Agung Bagus Pramana',
                'username' => 'bagus.pramana',
                'email' => 'bagus.pramana@bappeda.test',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'nama_depan' => 'Gusti Agung Bagus',
                'nama_belakang' => 'Pramana',
                'status_aktif' => true,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user + ['email_verified_at' => now()]
            );
        }
    }
}
