<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tema;
use App\Models\Urusan;
use App\Models\Bidang;
use App\Models\Frekuensi;
use App\Models\DataField;
use App\Models\DataUpload;
use App\Models\DataValue;
use App\Models\Bookmark;

class Data extends Model
{
   protected $table = 'data';
   protected $primaryKey = 'id_data';

   protected $fillable = [
       'id_user', 
       'id_upload',
       'nama_data',
       'deskripsi',
       'id_tema',
       'id_urusan',
       'id_bidang',
       'id_frekuensi',
       'satuan',
       'sumber',
       'tahun_terbit',
       'informasi_tambahan',
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
   
   public function katakunci()
   {
       return $this->belongsToMany(Katakunci::class, 'data_katakunci_pivot', 'id_data', 'id_katakunci');
   }

   public function cd ()
   {
       return $this->hasMany(DataField::class, 'id_data');
   }

   public function uploads()
   {
       return $this->hasMany(DataUpload::class, 'id_data', 'id_data');
   }

   public function values() {
       return $this->hasMany(DataValue::class, 'id_data', 'id_data');
   }

   public function bookmarks()
   {
       return $this->hasMany(Bookmark::class, 'dataset_id', 'id_data');
   }
}
