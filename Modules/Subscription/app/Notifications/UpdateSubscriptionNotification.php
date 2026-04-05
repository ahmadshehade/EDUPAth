<?php

namespace Modules\Subscription\Notifications;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;

use Illuminate\Notifications\Messages\MailMessage;

class UpdateSubscriptionNotification extends BaseNotification
{
    use Queueable;

    protected $subscription;

    public $userId;
    /**
     * Create a new notification instance.
     */
    public function __construct($userId, $subscription)
    {
        $this->subscription = $subscription;
        $this->userId = $userId;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $subscription = $this->subscription;

        return (new MailMessage)
            ->subject('Subscription Updated Successfully ✅')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your subscription has been updated successfully.')
            ->line('Here are your updated subscription details:')
            ->line('Plan Name: ' . $subscription->plan->name)
            ->line('Price: $' . $subscription->plan->price)
            ->line('Status: ' . ucfirst($subscription->status))
            ->line('Start Date: ' . $subscription->starts_at)
            ->line('End Date: ' . $subscription->ends_at)
            ->action('View Subscription', url('/subscriptions/' . $subscription->id))
            ->line('If you did not make this change, please contact support immediately.')
            ->line('Thank you for using our platform! 🚀');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'user_id' => $this->userId,
            'subscription' => $this->subscription,
            'type' => self::class,
        ];
    }
}
