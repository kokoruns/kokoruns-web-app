<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyAdmin extends Model
{
    use HasFactory;
    protected $fillable = [
    
        's_no', 'company_id', 'sub_admin_id', 'sub_admin_name'
        ];
}
