<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyBranch extends Model
{
    use HasFactory;
    protected $fillable = [
    
        'company_id',
        'branch_id',
        'branch_name',
        'branch_manager',
        'branch_address',
        'branch_state',
        'branch_lga',
        'branch_phone',
        
        ];
}
