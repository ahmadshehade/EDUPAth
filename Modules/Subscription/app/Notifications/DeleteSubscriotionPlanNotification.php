<?php

namespace Modules\Subscription\Notifications;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class DeleteSubscriotionPlanNotification extends BaseNotification
{
    use Queueable;

    protected  $userId;
    protected  $data;

    public function __construct(int $userId, array $data)
    {
        $this->userId = $userId;
        $this->data   = $data;
    }

    /**
     * Channels
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Format data safely
     */
    protected function format(): array
    {
        return [
            'id'       => $this->data['plan_id'] ?? null,
            'name'     => $this->data['name'] ?? 'N/A',
            'price'    => number_format($this->data['price'] ?? 0, 2),
            'interval' => $this->data['interval'] ?? 'N/A',
        ];
    }

    /**
     * Mail
     */
    public function toMail($notifiable): MailMessage
    {
        $plan = $this->format();

        return (new MailMessage)
            ->subject('🗑️ Subscription Plan Deleted')
            ->greeting("Hello {$notifiable->name} 👋")
            ->line("The subscription plan **{$plan['name']}** has been deleted.")
            ->line("• Price: \${$plan['price']}")
            ->line("• Interval: {$plan['interval']}")
            ->line('If you were subscribed to this plan, your subscription may be affected.')
            ->line('For more details, please contact support.')
            ->line('Thank you for using our platform 🚀');
    }

    /**
     * Database payload
     */
    public function toArray($notifiable): array
    {
        $plan = $this->format();

        return [
            'user_id'       => $this->userId,
            'plan_id'       => $plan['id'],
            'plan_name'     => $plan['name'],
            'plan_price'    => $plan['price'],
            'plan_interval' => $plan['interval'],
            'message'       => 'Subscription plan deleted',
            'type'          => self::class,
        ];
    }
}
