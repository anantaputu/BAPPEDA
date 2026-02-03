<?php

namespace App\Models;

<<<<<<< HEAD
=======
use Illuminate\Database\Eloquent\Factories\HasFactory;
>>>>>>> origin
use Illuminate\Database\Eloquent\Model;

class DataFieldMapping extends Model
{
<<<<<<< HEAD
    // PENTING: Nama tabel harus sama persis dengan yang di migrasi
=======
    use HasFactory;

    // PENTING: Mengarah ke tabel 'data_mappings' sesuai migrasi Anda
>>>>>>> origin
    protected $table = 'data_mappings'; 
    
    // Primary Key (default 'id', jadi tidak wajib ditulis, tapi boleh biar jelas)
    protected $primaryKey = 'id';

    // Kolom yang boleh diisi (Mass Assignment)
<<<<<<< HEAD
=======
    // Pastikan ini sesuai dengan kolom di tabel database Anda
>>>>>>> origin
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
<<<<<<< HEAD
=======
        // Pastikan 'id_upload' adalah foreign key yang benar
>>>>>>> origin
        return $this->belongsTo(DataUpload::class, 'id_upload', 'id_upload');
    }

    /**
     * Relasi ke DataField (Master Field)
     */
    public function field()
    {
<<<<<<< HEAD
=======
        // Pastikan 'id_field' adalah foreign key yang benar
>>>>>>> origin
        return $this->belongsTo(DataField::class, 'id_field', 'id_field');
    }
}