<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Association extends Model
{
    use HasFactory;
    protected $fillable = [
    
        'association_name', 'association_id', 'cac', 'association_address', 'association_director', 'association_email', 'association_contact_email', 'phone', 'website', 'about', 'locations', 'main_office_location_state', 'main_office_location_lga', 'facebook', 'linkedin', 'instagram', 'tags', 'field', 'verified', 'author', 'logo', 'founded_month', 'founded_year'

        ];
}
