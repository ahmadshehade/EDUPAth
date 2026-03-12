<?php

namespace Modules\CourseManagement\Models;

use App\Enums\EnrollmentStatus;
use App\Enums\UserRoles;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\CourseManagement\Database\Factories\EnrollmentFactory;

class Enrollment extends Model {
    protected $fillable = [
        'user_id',
        'course_id',
        'enrolled_at',
        'progress',
        'completed_at',
        'status',
        'last_accessed_at'
    ];

    /**
     * Summary of casts
     * @var array
     */
    protected $casts = [
        'enrolled_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'completed_at' => 'datetime',
        'progress' => 'integer',
        'status' => EnrollmentStatus::class,
    ];
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Summary of course
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Course, Enrollment>
     */
    public function course() {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Summary of lessonProgress
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<LessonProgress, Enrollment>
     */
    public function lessonProgress() {
        return $this->hasMany(LessonProgress::class);
    }

    /**
     * Summary of scopeVisibleToUser
     * @param mixed $query
     * @param mixed $user
     */
    public function scopeVisibleToUser($query, $user) {
        if ($user->hasRole(UserRoles::Admin->value)) {
            return $query;
        }
        if ($user->hasRole(UserRoles::Instructor->value)) {
            return $query->whereHas('course', function ($q) use ($user) {
                $q->where('instructor_id', $user->id);
            });
        }
        if ($user->hasRole(UserRoles::Student->value)) {
            return $query->where('user_id', $user->id);
        }
        return $query->whereRaw('1 = 0');
    }
}
