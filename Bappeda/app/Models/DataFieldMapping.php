<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataFieldMapping extends Model
{
    use HasFactory;

    // PENTING: Mengarah ke tabel 'data_mappings' sesuai migrasi Anda
    protected $table = 'data_mappings'; 
    
    // Primary Key (default 'id', jadi tidak wajib ditulis, tapi boleh biar jelas)
    protected $primaryKey = 'id';

    // Kolom yang boleh diisi (Mass Assignment)
    // Pastikan ini sesuai dengan kolom di tabel database Anda
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
        // Pastikan 'id_upload' adalah foreign key yang benar
        return $this->belongsTo(DataUpload::class, 'id_upload', 'id_upload');
    }

    /**
     * Relasi ke DataField (Master Field)
     */
    public function field()
    {
        // Pastikan 'id_field' adalah foreign key yang benar
        return $this->belongsTo(DataField::class, 'id_field', 'id_field');
    }
}