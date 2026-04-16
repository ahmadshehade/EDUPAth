<?php

namespace Modules\Payments\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Payments\Events\CreateInvoiceForInrollmentEvent;
use Modules\Payments\Events\CreateInvoiceForSubscriptionEvent;
use Modules\Payments\Events\SoftDeleteInvoiceEvent;
use Modules\Payments\Listeners\CreateInvoiceForEnrollmentListener;
use Modules\Payments\Listeners\CreateInvoiceForSubscriptionListener;
use Modules\Payments\Listeners\SoftDeleteInvoiceListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        CreateInvoiceForInrollmentEvent::class => [
            CreateInvoiceForEnrollmentListener::class
        ],
        CreateInvoiceForSubscriptionEvent::class => [
            CreateInvoiceForSubscriptionListener::class
        ],
        SoftDeleteInvoiceEvent::class=>[
            SoftDeleteInvoiceListener::class
        ]
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}
