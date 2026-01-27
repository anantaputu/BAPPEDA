<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DataUpload;
use App\Models\DataField;

class DataValue extends Model
{
    protected $table = 'data_values';
    protected $primaryKey = 'id_value';

    protected $fillable = [
        'id_upload',
        'id_field',
        'nilai'
    ];

    public function upload()
    {
        return $this->belongsTo(DataUpload::class, 'id_upload');
    }

    public function field()
    {
        return $this->belongsTo(DataField::class, 'id_field');
    }
}
