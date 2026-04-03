<?php

namespace Modules\Subscription\Models;

use Database\Factories\SubscriptionPlanFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Features\SupportWireModelingNestedComponents\BaseModelable;
use Modules\CourseManagement\Models\Course;
use Spatie\Translatable\HasTranslations;

// use Modules\Subscription\Database\Factories\SubscriptionPlanFactory;

class SubscriptionPlan extends Model {
    use HasFactory, HasTranslations;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'interval',
        'features',
        'interval_count',
        'is_active'
    ];

    protected  $table = 'subscription_plans';

    /**
     * Summary of casts
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'features' => 'array',
        'price' => 'decimal:2'
    ];
    /**
     * Summary of translatable
     * @var array
     */
    protected $translatable = ['name', 'description'];

    /**
     * Summary of subscriptions
     * @return HasMany<Subscription, SubscriptionPlan>
     */
    public function subscriptions(): HasMany {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    /**
     * Summary of newFactory
     * @return SubscriptionPlanFactory
     */
    protected static function newFactory(): SubscriptionPlanFactory {
        return SubscriptionPlanFactory::new();
    }

    /**
     * Summary of courses
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Course, SubscriptionPlan, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function courses() {
        return $this->belongsToMany(Course::class, 'course_subscription_plan');
    }
}
