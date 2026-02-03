<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    protected $casts = [
        'wajib' => 'boolean',
    ];

    /**
     * Relasi ke master data
     */
    public function data()
    {
        return $this->belongsTo(Data::class, 'id_data', 'id_data');
    }

    /**
     * Relasi ke mapping Excel
     */
    public function mappings()
    {
        return $this->hasMany(DataFieldMapping::class, 'id_field', 'id_field');
    }

    /**
     * Relasi ke nilai data
     */
    public function values()
    {
        return $this->hasMany(DataValue::class, 'id_field', 'id_field');
    }
}
