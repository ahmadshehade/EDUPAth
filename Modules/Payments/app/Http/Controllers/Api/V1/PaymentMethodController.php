<?php

namespace Modules\Payments\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Payments\Http\Requests\Api\V1\PaymentMethods\StorePaymentMethodRequest;
use Modules\Payments\Http\Requests\Api\V1\PaymentMethods\UpdatePaymentMethodRequest;
use Modules\Payments\Models\PaymentMethod;
use Modules\Payments\Services\PaymentMethodServices;

class PaymentMethodController extends Controller
{
    protected PaymentMethodServices $payment_service;

    public function __construct(PaymentMethodServices $payment_service)
    {
        $this->payment_service = $payment_service;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['name', 'description', 'code', 'type', 'is_active']);
        $paymentMethods = $this->payment_service->getAll($filters);
        return $this->successMessage('Successfully Get All Payment Method', $paymentMethods, 200);
    }

 

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentMethodRequest $request) {
        $paymentMethod=$this->payment_service->store($request->validated());
        return $this->successMessage('Successfully Make New Paymnt Method',$paymentMethod,201);
    }

    /**
     * Show the specified resource.
     */
    public function show(PaymentMethod $paymentMethod)
    {
        $data=$this->payment_service->get($paymentMethod);
        return $this->successMessage('Successfully Get Payment Method',$data,200);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod) {
        $data=$this->payment_service->update($paymentMethod,$request->validated());
        return $this->successMessage('Successfully Update Payment method',$data,200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentMethod $paymentMethod) {
        $success=$this->payment_service->destroy($paymentMethod);
        return $this->successMessage('Successfully Delete Payment Method',$success,200);
    }
}
