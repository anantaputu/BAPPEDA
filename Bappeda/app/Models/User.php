<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $fillable = [
        'username',
        'email',
        'password',
        'role_id',
        'nama_depan',
        'nama_belakang',
        'status_aktif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status_aktif' => 'boolean',
        ];
    }

    /* ================= RELATIONS ================= */

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function dataUploads()
    {
        return $this->hasMany(DataUpload::class, 'id_user');
    }

    /* ================= ACCESSOR ================= */

    public function getNamaLengkapAttribute()
    {
        return trim($this->nama_depan.' '.$this->nama_belakang);
    }
}
