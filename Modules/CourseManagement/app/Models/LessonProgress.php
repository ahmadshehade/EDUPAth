<?php

namespace Modules\CourseManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\CourseManagement\Database\Factories\LessonProgressFactory;

class LessonProgress extends Model {
    protected $fillable = ['enrollment_id', 'lesson_id', 'completed_at'];

    public function enrollment() {
        return $this->belongsTo(Enrollment::class);
    }

    public function lesson() {
        return $this->belongsTo(Lesson::class);
    }
}
