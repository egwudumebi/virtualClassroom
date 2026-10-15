<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'teacher_id',
        'subject',
        'grade_level',
        'max_students',
        'is_active',
        'class_code',
        'cover_image',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($classroom) {
            if (empty($classroom->class_code)) {
                $classroom->class_code = Str::random(8);
            }
        });
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'classroom_user')
                    ->withPivot('status', 'joined_at')
                    ->withTimestamps();
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function approvedStudents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'classroom_user')
                    ->wherePivot('status', 'approved')
                    ->withPivot('joined_at')
                    ->withTimestamps();
    }

    public function pendingStudents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'classroom_user')
                    ->wherePivot('status', 'pending')
                    ->withTimestamps();
    }
}
