<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataValue extends Model
{
    protected $table = 'data_values';
    protected $guarded = [];

    // Tambahkan ini agar outputnya murni angka, bukan string "10.5"
    protected $casts = [
        'nilai' => 'double', 
    ];

    public function data()
    {
        return $this->belongsTo(Data::class, 'id_data', 'id_data');
    }
}