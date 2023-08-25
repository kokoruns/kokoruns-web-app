<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = 'token';
    protected $fillable = [
        'email',
        'token',
        'expires_in',
        'user_id',
        'used',
        'action',
    ];
}
