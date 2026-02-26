<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Katakunci extends Model
{
    protected $table = 'katakunci';
    protected $primaryKey = 'id_katakunci';
    protected $guarded = [];

    public function data()
    {
        return $this->belongsToMany(Data::class, 'data_katakunci_pivot', 'id_data', 'id_katakunci');
    }
}