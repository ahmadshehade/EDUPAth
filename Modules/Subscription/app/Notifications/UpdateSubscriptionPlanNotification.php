<?php

namespace Modules\Subscription\Notifications;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class UpdateSubscriptionPlanNotification extends BaseNotification  
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
            'id'       => $this->plan->id ?? null,
            'name'     => $this->plan->name ?? 'N/A',
            'price'    => number_format($this->plan->price ?? 0, 2),
            'interval' => $this->plan->interval ?? 'N/A',
            'count'    => $this->plan->interval_count ?? 1,
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $plan = $this->formatPlan();

        return (new MailMessage)
            ->subject('✏️ Subscription Plan Updated')
            ->greeting("Hello {$notifiable->name} 👋")
            ->line('A subscription plan has been updated successfully.')
            ->line('Updated details:')
            ->line("• **Name:** {$plan['name']}")
            ->line("• **Price:** \${$plan['price']}")
            ->line("• **Duration:** {$plan['interval']} ({$plan['count']} times)")
            ->action('View Plan', url("/plans/{$plan['id']}"))
            ->line('If you did not expect this change, please contact support.')
            ->line('Thank you for using our platform 🚀');
    }

    public function toArray($notifiable): array
    {
        return [
            'user_id'   => $this->userId,
            'plan_id'   => $this->plan->id ?? null,
            'plan_name' => $this->plan->name ?? 'N/A',
            'message'   => 'Subscription plan updated',
            'type'      => self::class,
        ];
    }
}