<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lga extends Model
{
    protected $table = 'lgas';
    protected $fillable = [
        'name',
        'state_id',
    ];
}
