<?php

namespace Modules\Subscription\Notifications;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\Subscription\Models\Subscription;

class CreateSubscriptionNotification extends BaseNotification 
{
    use Queueable;

    protected int $userId;
    protected Subscription $subscription;

    /**
     * Create a new notification instance.
     */
    public function __construct(int $userId, Subscription $subscription)
    {
        $this->userId = $userId;
        $this->subscription = $subscription;
    }

    /**
     * Channels
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Mail Notification
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Subscription Created Successfully')
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your subscription has been created successfully.')
            ->line('Plan: ' . $this->subscription->plan->name ?? 'N/A')
            ->line('Start Date: ' . $this->subscription->starts_at)
            ->line('End Date: ' . $this->subscription->ends_at)
            ->action('View Subscription', url('/subscriptions/' . $this->subscription->id))
            ->line('Thank you for using our application!');
    }

    /**
     * Database Notification
     */
    public function toArray($notifiable): array
    {
        return [
            'user_id'       => $this->userId,
            'subscription_id' => $this->subscription->id,
            'plan_name'     => optional($this->subscription->plan)->name,
            'start_date'    => $this->subscription->start_date,
            'end_date'      => $this->subscription->end_date,
            'message'       => 'Subscription created successfully',
            'type'          => self::class,
        ];
    }
}