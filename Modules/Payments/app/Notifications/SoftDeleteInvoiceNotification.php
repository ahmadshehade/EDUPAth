<?php

namespace Modules\Payments\Notifications;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;


use Illuminate\Notifications\Messages\MailMessage;

class SoftDeleteInvoiceNotification extends BaseNotification
{
    use Queueable;

    public $actor_id;
    public $invoices;

    public function __construct($actor_id, $invoices)
    {
        $this->actor_id = $actor_id;
        $this->invoices = $invoices;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $isActor = $notifiable->id === $this->actor_id;

        return (new MailMessage)
            ->subject('Invoice Deleted')
            ->line(
                $isActor
                    ? 'You have successfully deleted invoice(s).'
                    : 'An invoice associated with your account has been deleted.'
            )
            ->line('Invoice IDs: ' . implode(', ', $this->invoices))
            ->action('View Details', url('/invoices'))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable): array
    {
        return [
            'actor_id' => $this->actor_id,
            'invoice_ids' => $this->invoices,
            'type' => self::class,
        ];
    }
}
