<?php

namespace Modules\Subscription\Notifications;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class SubscriptionPlanCreateNotification extends BaseNotification 
{
    use Queueable;

    protected int $userId;
    protected object $plan;

    public function __construct(int $userId, object $plan)
    {
        $this->userId = $userId;
        $this->plan   = $plan;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Format plan data
     */
    protected function formatPlan(): array
    {
        return [
            'name'     => $this->plan->name ?? 'N/A',
            'price'    => number_format($this->plan->price ?? 0, 2),
            'interval' => $this->plan->interval ?? 'N/A',
            'count'    => $this->plan->interval_count ?? 1,
            'id'       => $this->plan->id ?? null,
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $plan = $this->formatPlan();

        return (new MailMessage)
            ->subject('💳 New Subscription Plan Created')
            ->greeting("Hello {$notifiable->name} 👋")
            ->line('A new subscription plan has been successfully created.')
            ->line('Here are the details:')
            ->line("• **Name:** {$plan['name']}")
            ->line("• **Price:** \${$plan['price']}")
            ->line("• **Duration:** {$plan['interval']} ({$plan['count']} times)")
            ->action('View Plan', url("/plans/{$plan['id']}"))
            ->line('You can manage or assign this plan from your dashboard.')
            ->line('Thank you for using our platform 🚀');
    }

    public function toArray($notifiable): array
    {
        return [
            'user_id'   => $this->userId,
            'plan_id'   => $this->plan->id ?? null,
            'plan_name' => $this->plan->name ?? 'N/A',
            'type'      => self::class,
        ];
    }
}