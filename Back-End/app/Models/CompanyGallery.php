<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyGallery extends Model
{
    use HasFactory;

    protected $fillable = [
    
        'company_id',
        'user_id',
        'gallery_id',
        'image',
        'image_title',
       
        ];
}
