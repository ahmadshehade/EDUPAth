<?php

namespace Modules\CourseManagement\Models;

use App\Enums\UserRoles;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

// use Modules\CourseManagement\Database\Factories\SectionFactory;

class Section extends Model {
    use HasFactory, HasTranslations;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'course_id',
        'title',
        'slug',
        'description',
        'order',
        'is_published',
    ];


    protected $guarded = ['duration'];

    protected $casts = [
        'course_id'=>'integer',
        'is_published'=>'boolean',
        'duration'=>'integer',
        'order'=>'integer'
    ];
    protected $translatable = ['title', 'description'];
    /**
     * Summary of course
     * @return BelongsTo<Course, Section>
     */
    public function course(): BelongsTo {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Summary of getCreatedAtAttribute
     * @param mixed $value
     * @return string
     */
    public function getCreatedAtAttribute($value) {
        return Carbon::parse($value)->format('Y-m-d H:i');
    }

    /**
     * Summary of getUpdatedAtAttribute
     * @param mixed $value
     * @return string
     */
    public function getUpdatedAtAttribute($value) {
        return Carbon::parse($value)->format('Y-m-d H:i');
    }

    // protected static function newFactory(): SectionFactory
    // {
    //     // return SectionFactory::new();
    // }


        /**
     * Summary of scopeVisibleFor
     * @param mixed $query
     * @param mixed $user
     */
    public function scopeVisibleForSection($query, $user) {
        if ($user->hasRole(UserRoles::Admin->value)) {
            return $query;
        } elseif ($user->hasRole(UserRoles::Instructor->value)) {
            return $query->whereHas(
                'course',
                function ($qu) use ($user) {
                    $qu->where('instructor_id',$user->id);
                }
            );
        } elseif ($user->hasRole(UserRoles::Student->value)) {
            return $query->where('is_published', true);
        } else {
            return $query->whereRaw('0=1');
        }
    }
}
