<?php

namespace Modules\Payments\Models;

use Database\Factories\PaymentMethodFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Payments\Database\Factories\PaymentMethodFactory;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'payment_methods';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'code',
        'type',
        'is_active',
        'config'
    ];


    /**
     * Summary of newFactory
     * @return PaymentMethodFactory
     */
    protected static function newFactory()
    {
        return PaymentMethodFactory::new();
    }
}
