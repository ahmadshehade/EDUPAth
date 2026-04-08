<?php

namespace Modules\Subscription\Models;

use App\Enums\UserRoles;
use App\Models\User;
use Carbon\Carbon;
use Database\Factories\SubscriptionFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Payments\Models\Invoice;

// use Modules\Subscription\Database\Factories\SubscriptionFactory;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'plan_id',
        'starts_at',
        'ends_at',
        'status'
    ];

    /**
     * Summary of user
     * @return BelongsTo<User, Subscription>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Summary of plan
     * @return BelongsTo<SubscriptionPlan, Subscription>
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    /**
     * Summary of scopeFilterable
     * @param mixed $user
     * @param mixed $query
     */
    public function scopeFilterable($query, $user)
    {
        if ($user->hasRole(UserRoles::Admin->value)) {
            return $query;
        }
        if ($user->hasRole(UserRoles::Student->value)) {
            return $query->where('user_id', $user->id);
        }
        return $query->whereRaw('0=1');
    }

    /**
     * Summary of invoices
     * @return HasMany<Invoice, Subscription>
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'subscription_id');
    }
}
