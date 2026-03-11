<?php

namespace Modules\CourseManagement\Models;

use App\Enums\LessonType;
use App\Enums\UserRoles;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

// use Modules\CourseManagement\Database\Factories\LessonFactory;

class Lesson extends Model implements HasMedia {
    use HasFactory, HasTranslations, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'section_id',
        'content',
        'type',
        'order',
        'live_url',
        'live_start_at',
        'live_end_at',
        'live_status'
    ];

    protected $translatable = ['title'];

    protected $casts = [

        'type' => LessonType::class,
        'order' => 'integer',
    ];


    /**
     * Summary of section
     * @return BelongsTo<Section, Lesson>
     */
    public function section(): BelongsTo {
        return $this->belongsTo(Section::class, 'section_id');
    }

    // protected static function newFactory(): LessonFactory
    // {
    //     // return LessonFactory::new();
    // }
    /**
     * Summary of serializeDate
     * @param DateTimeInterface $date
     * @return string
     */
    public  function serializeDate(DateTimeInterface $date) {
        return $date->format('Y-m-d H:i');
    }

    /**
     * Summary of scopeVisibleForLesson
     * @param mixed $query
     * @param mixed $user
     */
    public function scopeVisibleForLesson($query, $user) {
        if ($user->hasRole(UserRoles::Admin->value)) {
            return $query;
        }
        if ($user->hasRole(UserRoles::Instructor->value)) {
            return $query->whereHas('section.course', function ($q) use ($user) {
                $q->where('instructor_id', $user->id);
            });
        }
        return $query;
    }


    /**
     * Summary of registerMediaCollections
     * @return void
     */
    public function registerMediaCollections(): void {
        $this->addMediaCollection('videos');
        $this->addMediaCollection('files');
        $this->addMediaCollection('assignments');
    }
}
