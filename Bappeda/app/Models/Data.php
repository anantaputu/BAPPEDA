<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tema;
use App\Models\Urusan;
use App\Models\Bidang;
use App\Models\Frekuensi;
use App\Models\DataField;
use App\Models\DataUpload;

class Data extends Model
{
    protected $table = 'data';
    protected $primaryKey = 'id_data';

    protected $fillable = [
        'nama_indikator',
        'deskripsi',
        'id_tema',
        'id_urusan',
        'id_bidang',
        'id_frekuensi',
        'kata_kunci',
        'satuan',
        'sumber',
        'status'
    ];

    /* ================= RELATIONS ================= */

     public function tema()
    {
        return $this->belongsTo(Tema::class, 'id_tema', 'id_tema');
    }

    public function urusan()
    {
        return $this->belongsTo(Urusan::class, 'id_urusan', 'id_urusan');
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class, 'id_bidang', 'id_bidang');
    }

    public function frekuensi()
    {
        return $this->belongsTo(Frekuensi::class, 'id_frekuensi', 'id_frekuensi');
    }

    public function fields()
    {
        return $this->hasMany(DataField::class, 'id_data');
    }

    public function uploads()
    {
        return $this->hasMany(DataUpload::class, 'id_data');
    }
}
