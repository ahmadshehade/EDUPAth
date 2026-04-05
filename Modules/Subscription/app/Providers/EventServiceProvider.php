<?php

namespace Modules\Subscription\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Subscription\Events\CreateSubscriptionEvent;
use Modules\Subscription\Events\DeleteSubscriptionEvent;
use Modules\Subscription\Events\DeleteSubscriptionPlanEvent;
use Modules\Subscription\Events\SubscriptionPlanCreatedEvent;
use Modules\Subscription\Events\UpdateSubscriptionEvent;
use Modules\Subscription\Events\UpdateSubscriptionPlanEvent;
use Modules\Subscription\Listeners\CreateSubsciptionListener;
use Modules\Subscription\Listeners\DeleteSubscriptionListener;
use Modules\Subscription\Listeners\DeleteSubscriptionPlanListener;
use Modules\Subscription\Listeners\SubscriptionPlanCreatedListener;
use Modules\Subscription\Listeners\UpdateSubscriptionListener;
use Modules\Subscription\Listeners\UpdateSubscriptionPlanListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        SubscriptionPlanCreatedEvent::class => [
            SubscriptionPlanCreatedListener::class,
        ],
        UpdateSubscriptionPlanEvent::class => [
            UpdateSubscriptionPlanListener::class,
        ],
        DeleteSubscriptionPlanEvent::class => [
            DeleteSubscriptionPlanListener::class,
        ],
        CreateSubscriptionEvent::class => [
            CreateSubsciptionListener::class,
        ],
        UpdateSubscriptionEvent::class => [
            UpdateSubscriptionListener::class,
        ],
        DeleteSubscriptionEvent::class => [
            DeleteSubscriptionListener::class,
        ],
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
