<?php

namespace Modules\Payments\Notifications;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CreatePaymentMethodNotification extends BaseNotification  implements ShouldQueue
{
    use Queueable;


    public $user_id;

    public $paymentMethod;
    /**
     * Create a new notification instance.
     */
    public function __construct($user_id, $paymentMethod)
    {
        $this->user_id = $user_id;
        $this->paymentMethod = $paymentMethod;
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
    

        return (new MailMessage)
            ->subject('Payment Method Created Successfully 💳')

            ->greeting('Hello ' . $notifiable->name . ',')

            ->line('A new payment method has been added to the system.')

            ->line('**Details:**')
            ->line('Name: ' . $this->paymentMethod->name)
            ->line('Type: ' . $this->paymentMethod->type)
            ->line('Code: ' . $this->paymentMethod->code)

            ->when($this->paymentMethod->description, function ($mail) {
                $mail->line('Description: ' . $this->paymentMethod->description);
            })

            ->line('Status: ' . ($this->paymentMethod->is_active ? 'Active ✅' : 'Inactive ❌'))

            ->when($this->paymentMethod->created_at, function ($mail) {
                $mail->line('Created At: ' . $this->paymentMethod->created_at->format('Y-m-d H:i'));
            })

            ->action('Manage Payment Methods', url('/dashboard/payment-methods'))

            ->line('If this action was not performed by you, please contact support immediately.')

            ->salutation('Regards, ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'user_id' => $this->user_id,
            'paymentMethod' => $this->paymentMethod,
            'type' => self::class
        ];
    }
}
