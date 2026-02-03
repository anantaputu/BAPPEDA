<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Role extends Model
{
    // Nama tabel di database
    protected $table = 'roles';

    // Primary Key kustom sesuai migration
    protected $primaryKey = 'id_role';

    protected $fillable = [
        'nama_role',
    ];

    /**
     * Relasi balik ke User
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'id_role');
    }
}