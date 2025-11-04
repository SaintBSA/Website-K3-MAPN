<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusLog extends Model
{
    protected $table = 'status_logs'; 

    public $timestamps = false; 
    
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
    
    public function report()
    {
        return $this->belongsTo(Report::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}