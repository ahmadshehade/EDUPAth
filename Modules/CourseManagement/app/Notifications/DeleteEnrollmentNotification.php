<?php

namespace Modules\CourseManagement\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DeleteEnrollmentNotification extends Notification
{
    use Queueable;

    public $user_id;
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
            ->subject('Enrollment Deleted')
            ->line('An enrollment has been deleted.')
            ->line('Student: ' . ($this->data['user']['name'] ?? 'N/A'))
            ->line('Course: ' . ($this->data['course']['title'] ?? 'N/A'))
            ->line('Deleted by User ID: ' . $this->user_id)
            ->line('Thank you for using our system!');
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
