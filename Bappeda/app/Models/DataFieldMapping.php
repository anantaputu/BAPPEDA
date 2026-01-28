<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataFieldMapping extends Model
{
    protected $table = 'data_mappings'; 
    // kalau nama tabel kamu: data_mappings
    // kalau data_field_mappings, ganti sesuai tabel

    protected $primaryKey = 'id'; // default, aman

    protected $fillable = [
        'id_upload',
        'id_field',
        'excel_column',
    ];

    /**
     * Relasi ke upload
     */
    public function upload()
    {
        return $this->belongsTo(DataUpload::class, 'id_upload', 'id_upload');
    }

    /**
     * Relasi ke field
     */
    public function field()
    {
        return $this->belongsTo(DataField::class, 'id_field', 'id_field');
    }
}
