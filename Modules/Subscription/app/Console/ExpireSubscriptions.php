<?php

namespace Modules\Subscription\Console;

use App\Enums\SubscriptionStatus;
use Illuminate\Console\Command;
use Modules\Subscription\Models\Subscription;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

namespace Modules\Subscription\Console;

use App\Enums\SubscriptionStatus;
use Illuminate\Console\Command;
use Modules\Subscription\Models\Subscription;

class ExpireSubscriptions extends Command 
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Expire subscriptions that reached their end date';

    public function handle()
    {
        $expiredCount = Subscription::whereIn('status', [
                SubscriptionStatus::Active->value,
                SubscriptionStatus::Pending->value
            ])
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', now())
            ->update([
                'status' => SubscriptionStatus::Expired->value
            ]);

        $this->info("Expired {$expiredCount} subscriptions successfully.");
    }
}
