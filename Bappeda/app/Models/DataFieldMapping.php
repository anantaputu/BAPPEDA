<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataFieldMapping extends Model
{
    // PENTING: Nama tabel harus sama persis dengan yang di migrasi
    protected $table = 'data_mappings'; 
    
    // Primary Key (default 'id', jadi tidak wajib ditulis, tapi boleh biar jelas)
    protected $primaryKey = 'id';

    // Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'id_upload',
        'id_field',
        'excel_column',
    ];

    /**
     * Relasi ke DataUpload (Upload Induk)
     */
    public function upload()
    {
        return $this->belongsTo(DataUpload::class, 'id_upload', 'id_upload');
    }

    /**
     * Relasi ke DataField (Master Field)
     */
    public function field()
    {
        return $this->belongsTo(DataField::class, 'id_field', 'id_field');
    }
}