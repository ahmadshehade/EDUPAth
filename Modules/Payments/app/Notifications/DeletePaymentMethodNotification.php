<?php

namespace Modules\Payments\Notifications;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DeletePaymentMethodNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    public  $user_id;

    public $data;
    /**
     * Create a new notification instance.
     */
    public function __construct($user_id, $data)
    {
        $this->user_id = $user_id;
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
        return (new MailMessage)
            ->subject('Payment Method Delete Successfully 💳')

            ->greeting('Hello ' . $notifiable->name . ',')

            ->line('A  payment method has been Delete to the system.')

            ->line('**Details:**')
            ->line('Name: ' . $this->data['name'])
            ->line('Type: ' . $this->data['type'])
            ->line('Code: ' . $this->data['code'])

            ->when($this->data['description'], function ($mail) {
                $mail->line('Description: ' . $this->data['description']);
            })

            ->line('Status: ' . ($this->data['is_active'] ? 'Active ✅' : 'Inactive ❌'))

            ->when($this->data['created_at'], function ($mail) {
                $mail->line('Created At: ' . $this->data['created_at']);
            })

            ->when($this->data['updated_at'], function ($mail) {

                $mail->line('Updated At: ' . $this->data['updated_at']);
            })


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
            'data' => $this->data,
            'type' => self::class
        ];
    }
}
