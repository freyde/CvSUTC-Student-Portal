<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'majors',
    ];

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'program_course');
    }
}

