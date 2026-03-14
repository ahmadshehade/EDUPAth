<?php

namespace Modules\CourseManagement\Notifications;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DeleteLessonNotification extends BaseNotification {
    use Queueable;

    public $user_id;

    public $data;
    /**
     * Create a new notification instance.
     */
    public function __construct($data, $user_id) {
        $this->data = $data;
        $this->user_id = $user_id;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage {
        $titleAr = $this->data['title']['ar'] ?? null;
        $titleEn = $this->data['title']['en'] ?? null;
        return (new MailMessage)
            ->subject('Lesson Deleted')
            ->line('A Lesson has been deleted from the platform.')
            ->line('Lesson Title (EN): ' . $titleEn)
            ->line('عنوان الدرس (AR): ' . $titleAr)
            ->line('Order:' . $this->data['order'])
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array {
        return [
            'user_id' => $this->user_id,
            'lesson' => $this->data,
            'type' => static::class
        ];
    }
}
