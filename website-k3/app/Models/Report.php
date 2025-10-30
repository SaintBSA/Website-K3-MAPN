<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public function statusLogs()
{
    return $this->hasMany(StatusLog::class, 'report_id');
}
}
