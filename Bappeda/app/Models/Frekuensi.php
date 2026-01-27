<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Data;

class Frekuensi extends Model
{
    protected $table = 'frekuensi';
    protected $primaryKey = 'id_frekuensi';

    protected $fillable = ['nama_frekuensi'];

    public function data()
    {
        return $this->hasMany(Data::class, 'id_frekuensi');
    }
}
