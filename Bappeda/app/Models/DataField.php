<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Data;
use App\Models\DataValue;

class DataField extends Model
{
    protected $table = 'data_fields';
    protected $primaryKey = 'id_field';

    protected $fillable = [
        'id_data',
        'nama_field',
        'key_field',
        'tipe_field',
        'wajib'
    ];

    public function data()
    {
        return $this->belongsTo(Data::class, 'id_data');
    }

    public function values()
    {
        return $this->hasMany(DataValue::class, 'id_field');
    }
}
