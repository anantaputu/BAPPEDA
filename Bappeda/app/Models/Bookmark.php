<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    use HasFactory;

    // Beri tahu Laravel nama tabel aslinya
    protected $table = 'bookmark';

    // Beri tahu Laravel nama primary key-nya
    protected $primaryKey = 'id_bookmark';

    // Izinkan semua kolom diisi secara massal
    protected $guarded = []; 
}