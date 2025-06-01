<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    protected $table = 'educations';

    protected $fillable = [
        'user_id',
        'education_level',
        'school_name',
        'college_name',
        'course_name',
        'year_of_passing',
        'percentage',
        'is_currently_studying',
        'current_class',
        'current_school',
        'scholarship_status',
        'extra_curricular_activities',
    ];

    protected $casts = [
        'year_of_passing' => 'integer',
        'percentage' => 'float',
        'is_currently_studying' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
