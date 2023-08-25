<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OTP extends Model
{
    protected $table = 'otp';
    protected $fillable = [
        'phone',
        'otp',
        'expires_in',
        'user_id',
        'used',
    ];
}
