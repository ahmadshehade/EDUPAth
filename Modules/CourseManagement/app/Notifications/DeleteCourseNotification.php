<?php

namespace Modules\CourseManagement\Notifications;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DeleteCourseNotification extends BaseNotification {
    use Queueable;

    public  $user_id;

    public $data;

    /**
     * Summary of __construct
     * @param mixed $user_id
     * @param mixed $data
     */
    public function __construct($user_id, $data) {

        $this->user_id = $user_id;
        $this->data = $data;
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
            ->subject('Course Deleted')
            ->line('A course has been deleted from the platform.')
            ->line('Course Title (EN): ' . $titleEn)
            ->line('عنوان الدورة (AR): ' . $titleAr)
            ->line('Published: ' . ($this->data['is_published'] ? "True" : "False"))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array {
        return [
            'user_id' => $this->user_id,
            'course' => $this->data,
            'type' => static::class
        ];
    }
}
