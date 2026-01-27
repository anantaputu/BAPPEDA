<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Data;

class Urusan extends Model
{
    protected $table = 'urusan';
    protected $primaryKey = 'id_urusan';

    protected $fillable = ['nama_urusan'];

    public function data()
    {
        return $this->hasMany(Data::class, 'id_urusan');
    }
}
