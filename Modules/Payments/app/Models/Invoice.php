<?php

namespace Modules\Payments\Models;

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\CourseManagement\Models\Enrollment;
use Modules\Subscription\Models\Subscription;

// use Modules\Payments\Database\Factories\InvoiceFactory;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'enrollment_id',
        'subscription_id',
        'payment_method_id',
        'amount',
        'currency',
        'metadata',
    ];


    /**
     * Summary of guarded
     * @var array
     */
    protected $guarded = [
        'user_id',
        'invoice_number',
        'status',
        'issued_at',
        'paid_at',
    ];


    /**
     * Summary of user
     * @return BelongsTo<User, Invoice>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Summary of enrollment
     * @return BelongsTo<Enrollment, Invoice>
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    /**
     * Summary of paymentMethod
     * @return BelongsTo<PaymentMethod, Invoice>
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    /**
     * Summary of scopeVisibleFor
     * @param mixed $query
     * @param mixed $user
     */
    public function scopeVisibleFor($query, $user)
    {
        if ($user->hasRole(UserRoles::Admin->value)) {
            return $query;
        }

        if ($user->hasRole(UserRoles::Student->value)) {
            return $query->where('user_id', $user->id);
        }

        return $query->whereRaw('0 = 1');
    }

    // protected static function newFactory(): InvoiceFactory
    // {
    //     // return InvoiceFactory::new();
    // }
}
