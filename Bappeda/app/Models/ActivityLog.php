<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id_log';
    protected $fillable = ['id_user', 'id_data', 'action', 'target_name', 'description', 'payload', 'ip_address'];

    protected $casts = [
        'payload' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}