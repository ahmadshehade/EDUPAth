<?php

namespace Modules\Payments\Services;

use App\Enums\InvoiceStatus;
use App\Enums\NameOfCache;
use App\Traits\FilterableServiceTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\CourseManagement\Models\Enrollment;
use Modules\Payments\Events\CreateInvoiceForInrollmentEvent;
use Modules\Payments\Events\CreateInvoiceForSubscriptionEvent;
use Modules\Payments\Events\SoftDeleteInvoiceEvent;
use Modules\Payments\Models\Invoice;
use Modules\Subscription\Models\Subscription;

class InvoiceService
{

    use FilterableServiceTrait;
    /**
     * Summary of storeFromEnrollment
     * @param Enrollment $enrollment
     * @param array $data
     * @return Invoice
     */
    public function storeFromEnrollment(Enrollment $enrollment, array $data)
    {
        $invoice = new Invoice();
        $invoice->user_id = $enrollment->user_id;
        $invoice->enrollment_id = $enrollment->id;
        $invoice->payment_method_id = $data["payment_method_id"];
        $invoice->invoice_number = 'INV-' . now()->timestamp . '-' . Str::random(5);
        $invoice->amount = $enrollment->course->price;
        $invoice->issued_at = now();
        $invoice->status = InvoiceStatus::PENDING->value;
        $invoice->save();
        Cache::tags([NameOfCache::Invoice->value])->flush();
        DB::afterCommit(function () use ($invoice) {
            event(new CreateInvoiceForInrollmentEvent(Auth::id(), $invoice));
        });
        return $invoice;
    }


    /**
     * Summary of storeInvoiceForSubscription
     * @param Subscription $subscription
     * @param array $data
     * @return Invoice
     */
    public function storeInvoiceForSubscription(Subscription $subscription, array $data)
    {
        $invoice = new Invoice();
        $invoice->user_id = $subscription->user_id;
        $invoice->subscription_id = $subscription->id;
        $invoice->payment_method_id = $data["payment_method_id"];
        $invoice->invoice_number = 'INV-' . now()->timestamp . '-' . Str::random(5);
        $invoice->amount = $subscription->plan->price;
        $invoice->issued_at = now();
        $invoice->status = InvoiceStatus::PENDING->value;
        $invoice->save();
        Cache::tags([NameOfCache::Invoice->value])->flush();
        DB::afterCommit(function () use ($invoice) {
            event(new CreateInvoiceForSubscriptionEvent($invoice));
        });
        return $invoice;
    }

    /**
     * Summary of updateInvoiceForInrollment
     * @param Enrollment $enrollment
     * @param array $data
     * @return Invoice
     */
    public function updateInvoiceForInrollment(Enrollment $enrollment, array $data)
    {
        $invoice = $enrollment->invoices()->first();
        $invoice->status = InvoiceStatus::FAILED->value;
        $invoice->metadata = array_merge(
            (array) $invoice->metadata,
            ['reason' => 'Enrollment updated, new invoice issued']
        );
        $invoice->save();
        $invoice->delete();
        $newInvoice = $this->storeFromEnrollment($enrollment, $data);
        return $newInvoice;
    }

    /**
     * Summary of updateInvoiceForSubscription
     * @param Subscription $subscription
     * @param array $data
     * @return Invoice
     */
    public function updateInvoiceForSubscription(Subscription $subscription, array $data)
    {
        $invoice = $subscription->invoices()->first();
        $invoice->status = InvoiceStatus::PENDING->value;
        $invoice->metadata = array_merge(
            (array) $invoice->metadata,
            ['reason' => 'Subscription updated, new invoice issued']
        );
        $invoice->save();
        Cache::tags([NameOfCache::Invoice->value])->flush();
        $newInvoice = $this->storeInvoiceForSubscription($subscription, $data);
        return $newInvoice;
    }

    /**
     * Summary of cansleInvoiceForSubscription
     * @param Subscription $subscription
     * @return bool
     */
    public function cansleInvoiceForSubscription(Subscription $subscription)
    {
        $invoiceIds = $subscription->invoices()->pluck('id');
        $subscription->invoices()->update([
            'status' => InvoiceStatus::FAILED->value,
        ]);
        $subscription->invoices()->delete();
        Cache::tags([NameOfCache::Invoice->value])->flush();
        event(new SoftDeleteInvoiceEvent(Auth::id(), $subscription->user_id, $invoiceIds));
        return true;
    }

    /**
     * Summary of cansleInvoiceForEnrollment
     * @param Enrollment $enrollment
     * @return bool
     */
    public function cansleInvoiceForEnrollment(Enrollment $enrollment)
    {
        $invoiceIds = $enrollment->invoices()->pluck('id');
        $enrollment->invoices()->update([
            'status' => InvoiceStatus::FAILED->value,
        ]);
        $enrollment->invoices()->delete();
        event(new SoftDeleteInvoiceEvent(Auth::id(), $enrollment->user_id, $invoiceIds));
        return true;
    }

    /**
     * Summary of generateKey
     * @param array $filters
     * @return string
     */
    public function generateKey(array $filters)
    {
        ksort($filters);
        $user = Auth::user();
        $userKey = $user ? $user->id . '_' . json_encode($user->roles->pluck('name')->toArray()) : "guest";
        $cacheKey = $userKey . "_" . md5(json_encode($filters));
        return $cacheKey;
    }

    /**
     * Summary of getAll
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     */
    public function  getAll(array $filters )
    {
        return Cache::tags(NameOfCache::Invoice->value)->remember(
            $this->generateKey($filters),
            now()->addMinute(),
            function () use ($filters) {
                $query = Invoice::query()->visibleFor(Auth::user());
                return $this->applyFilters($query, $filters);   
            }
        );
    }

     /**
      * Summary of get
      * @param Invoice $invoice
      * @return Invoice
      */
     public  function get(Invoice $invoice){
        return $invoice->load(['user','subscription','enrollment']);
     }
}
