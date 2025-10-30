<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusLog extends Model
{
    // Nama tabel disesuaikan dengan skema yang dibuat
    protected $table = 'status_logs'; 

    // Menonaktifkan timestamps bawaan karena kita menggunakan 'action_at'
    public $timestamps = false; 
    
    // Menetapkan kolom yang diizinkan untuk diisi
    protected $fillable = [
        'report_id',
        'user_id',
        'old_status',
        'new_status',
        'old_priority',
        'new_priority',
        'feedback',
        'action_at'
    ];
    
    // Definisikan relasi ke Report dan User
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}