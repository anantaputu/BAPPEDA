<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataValue extends Model
{
    protected $table = 'data_values';
    protected $guarded = [];

    public function data()
    {
        return $this->belongsTo(Data::class, 'id_data', 'id_data');
    }
}