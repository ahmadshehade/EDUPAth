<?php

namespace Modules\Payments\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Modules\Payments\Models\Invoice;
use Modules\Payments\Services\InvoiceService;

class InvoiceController extends Controller
{
    use AuthorizesRequests;
    protected InvoiceService $invoiceService;

    /**
     * Summary of __construct
     * @param InvoiceService $invoiceService
     */
    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Invoice::class);
        $filters = $request->only(['user_id', 'enrollment_id', 'subscription_id', 'payment_method_id', 'invoice_number', 'status']);
        $invoices = $this->invoiceService->getAll($filters);
        return $this->successMessage('Successfully Get All Invoices .', $invoices, 200);
    }
    /**
     * Show the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $data = $this->invoiceService->get($invoice);
        return $this->successMessage('Successfully  Get Invoice .', $data, 200);
    }
}
