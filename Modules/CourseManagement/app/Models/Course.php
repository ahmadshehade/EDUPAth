<?php

namespace Modules\CourseManagement\Models;

use App\Enums\UserRoles;
use App\Models\User;
use Carbon\Carbon;
use Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

// use Modules\CourseManagement\Database\Factories\CourseFactory;

class Course extends Model implements HasMedia {
    use HasFactory, InteractsWithMedia, HasTranslations;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'instructor_id',
        'category_id',
        'title',
        'slug',
        'description',
        'price',
        'is_published',
        'duration_hours',
        'level'
    ];
    /**
     * Summary of translatable
     * @var array
     */
    protected $translatable = [
        'title',
        'description',
        'level'
    ];

    /**
     * Summary of casts
     * @var array
     */
    protected $casts = [
        'instructor_id' => 'integer',
        'category_id'   => 'integer',
        'price'         => 'decimal:2',
        'is_published'  => 'boolean',
        'duration_hours' => 'integer',
    ];

    /**
     * Summary of instructor
     * @return BelongsTo<User, Course>
     */
    public function instructor(): BelongsTo {
        return $this->belongsTo(User::class, 'instructor_id');
    }
    /**
     * Summary of category
     * @return BelongsTo<Category, Course>
     */
    public  function category(): BelongsTo {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Summary of newFactory
     * @return CourseFactory
     */
    protected static function newFactory(): CourseFactory {
        return CourseFactory::new();
    }

    /**
     * Summary of getCreatedAtAttributes
     * @param mixed $value
     * @return string
     */
    public function getCreatedAtAttributes($value) {
        return Carbon::parse($value)->format('Y-m-d H:i');
    }

    /**
     * Summary of getUpdatedAtAttributes
     * @param mixed $value
     * @return string
     */
    public function getUpdatedAtAttributes($value) {
        return Carbon::parse($value)->format('Y-m-d H:i');
    }


    /**
     * Summary of scopeForVisibleCourse
     * @param mixed $q
     * @param mixed $user
     */
    public function scopeForVisibleCourse($q, $user) {
        if ($user->hasRole(UserRoles::Admin)) {
            return $q;
        }
        if ($user->hasAnyRole([
            UserRoles::Instructor->value,
            UserRoles::Student->value,
        ])) {
            return $q->where('is_published', true);
        }
        return $q->whereRaw('1 = 0');
    }

    /**
     * Summary of sections
     * @return HasMany<Section, Course>
     */
    public function sections():HasMany{
        return $this->hasMany(Section::class,'course_id');
    }



    /**
     * Summary of students
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<User, Course, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public  function students(){
        return $this->belongsToMany(User::class,'enrollments');
    }

    /**
     * Summary of enrollments
     * @return HasMany<Enrollment, Course>
     */
    public  function enrollments(){
        return  $this->hasMany(Enrollment::class,'course_id');
    }

    
}
