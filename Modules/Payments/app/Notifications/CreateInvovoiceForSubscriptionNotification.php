<?php

namespace Modules\Payments\Notifications;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class CreateInvovoiceForSubscriptionNotification extends BaseNotification
{
    use Queueable;

    protected $invoice;
    /**
     * Create a new notification instance.
     */
    public function __construct($invoice)
    {
        $this->invoice = $invoice;
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
            ->subject('Your Invoice Has Been Created 🧾')

            ->greeting('Hello ' . $notifiable->name . ',')

            ->line('A new invoice has been generated for your enrollment.')

            ->line('📄 Invoice Number: ' . $this->invoice->invoice_number)
            ->line('💰 Amount: ' . $this->invoice->amount . ' ' . $this->invoice->currency)
            ->line('📌 Status: ' . ucfirst($this->invoice->status))
            ->line('💳 Payment Method  ' . $this->invoice->paymentMethod->name)
            ->line('📅 Issued At: ' . $this->invoice->issued_at)

            ->action('View Invoice', url('/invoices/' . $this->invoice->id))

            ->line('Please complete the payment to activate your enrollment.')
            ->line('Thank you for choosing our platform!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'user_id' => $this->invoice->user_id,
            'invoice' => $this->invoice,
            'type' => self::class,
        ];
    }
}
