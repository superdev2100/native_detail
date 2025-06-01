<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Occupation extends Model
{
    use HasFactory;

    protected $table = 'occupations';

    protected $fillable = [
        'user_id',
        'occupation_type',
        'company_name',
        'job_title',
        'monthly_income',
        'work_location',
        'work_experience',
        'skills',
        'is_self_employed',
        'business_type',
        'business_address',
        'business_income',
        'government_scheme_benefits',
        'pension_status',
    ];

    protected $casts = [
        'monthly_income' => 'float',
        'work_experience' => 'integer',
        'is_self_employed' => 'boolean',
        'business_income' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
