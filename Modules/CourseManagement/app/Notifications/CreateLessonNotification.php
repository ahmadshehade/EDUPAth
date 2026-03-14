<?php

namespace Modules\CourseManagement\Notifications;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class CreateLessonNotification extends BaseNotification {
    use Queueable;

    public $user_id;

    public $lesson;
    /**
     * Create a new notification instance.
     */
    public function __construct($lesson, $user_id) {
        $this->user_id = $user_id;
        $this->lesson = $lesson;
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
        $titleEn = $this->lesson->title['en'] ?? $this->lesson->title;
        $titleAr = $this->lesson->title['ar'] ?? $this->lesson->title;
        return (new MailMessage)
            ->line('Make New Lesson')
            ->line('Lesson Title (EN): ' . $titleEn)
            ->line('عنوان الدرس (AR): ' . $titleAr)
            ->line('Content:'.$this->lesson->content)
            ->line('Type:'.$this->lesson->type->value)
            ->line('live_url :'.$this->lesson->live_url??'recorded')
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array {
        return [
            'user_id'=>$this->user_id,
            'lesson'=>$this->lesson,
            'type'=>static::class
        ];
    }
}
