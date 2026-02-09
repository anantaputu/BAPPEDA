<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Data;


class sumber extends Model
{
    protected $table = 'sumbers';
    protected $primaryKey = 'id_sumber';
    protected $fillable = ['nama_sumber'];

    public function data()
    {
        return $this->hasMany(Data::class, 'id_sumber');
    }
}
