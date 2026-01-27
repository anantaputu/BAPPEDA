<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Data;


class Tema extends Model

{
    protected $table = 'tema';
    protected $primaryKey = 'id_tema';

    protected $fillable = ['nama_tema'];

    public function data()
    {
        return $this->hasMany(Data::class, 'id_tema');
    }


}
