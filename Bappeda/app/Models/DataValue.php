<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataValue extends Model
{
   protected $table = 'data_values';

    protected $primaryKey = 'id_value'; 

    protected $fillable = ['id_data', 'tahun', 'nilai'];
    protected $guarded = [];

    protected $casts = [
        'nilai' => 'double', 
    ];

    public function data()
    {
        return $this->belongsTo(Data::class, 'id_data', 'id_data');
    }
}