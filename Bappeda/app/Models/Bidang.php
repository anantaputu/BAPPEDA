<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Data;

class Bidang extends Model
{
    protected $table = 'bidang';
    protected $primaryKey = 'id_bidang';

    protected $fillable = ['nama_bidang'];

    public function data()
    {
        return $this->hasMany(Data::class, 'id_bidang');
    }
}

