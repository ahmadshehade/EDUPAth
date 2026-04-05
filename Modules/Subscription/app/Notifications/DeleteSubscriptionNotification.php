<?php

namespace Modules\Subscription\Notifications;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class DeleteSubscriptionNotification extends BaseNotification
{
    use Queueable;

    protected $userId;
    protected $data;
    /**
     * Create a new notification instance.
     */
    public function __construct($userId, $data)
    {
        $this->userId = $userId;
        $this->data = $data;
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
        $data = $this->data;

        return (new MailMessage)
            ->subject('Subscription Cancelled ❌')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A subscription has been cancelled successfully.')
            ->line('Here are the details of the cancelled subscription:')
            ->line('Plan Name: ' . $data['plan'])
            ->line('End Date: ' . $data['ends_at'])
            ->action('View Subscriptions', url('/subscriptions'))
            ->line('If this action was not intended, please contact support immediately.')
            ->line('Thank you for using our platform.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'user_id' => $this->userId,
            'data' => $this->data,
            'type' => self::class
        ];
    }
}
