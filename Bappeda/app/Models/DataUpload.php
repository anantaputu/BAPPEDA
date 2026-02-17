<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Data;
use App\Models\User;

class DataUpload extends Model
{
    protected $table = 'data_uploads';
    protected $primaryKey = 'id_upload';

    protected $fillable = [
        'id_data',
        'id_user',
        'periode',
        'file_path',
        'status',
        'value'
    ];

    // MENGUBAH JSON MENJADI ARRAY OTOMATIS
    protected $casts = [
        'value' => 'array', 
    ];

    public function data()
    {
        return $this->belongsTo(Data::class, 'id_data', 'id_data');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function values()
    {
        return $this->hasMany(DataValue::class, 'id_upload');
    }
}
