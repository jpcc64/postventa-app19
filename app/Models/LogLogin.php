<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogLogin extends Model
{
    protected $table = 'authentication_logs';
    
    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'event_type',
        'successful',
        'remarks'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
