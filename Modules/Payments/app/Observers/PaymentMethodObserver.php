<?php

namespace Modules\Payments\Observers;

use Modules\Payments\Models\PaymentMethod;

use Modules\Payments\Services\PaymentMethodServices;

class  PaymentMethodObserver
{

  /**
   * Summary of created
   * @param PaymentMethod $paymentMethod
   * @return void
   */
  public function created(PaymentMethod $paymentMethod)
  {
    app(PaymentMethodServices::class)
      ->afterCreated($paymentMethod);
  }
  /**
   * Summary of updated
   * @param PaymentMethod $paymentMethod
   * @return void
   */
  public function updated(PaymentMethod $paymentMethod)
  {
    app(PaymentMethodServices::class)
      ->afterUpdated($paymentMethod);
  }

  /**
   * Summary of deleted
   * @param PaymentMethod $paymentMethod
   * @return void
   */
  public function deleted(PaymentMethod $paymentMethod)
  {
    app(PaymentMethodServices::class)
      ->afterDeletedData($paymentMethod->deleted_data);
  }
}
