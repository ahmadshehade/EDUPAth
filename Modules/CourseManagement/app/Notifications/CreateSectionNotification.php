<?php

namespace Modules\CourseManagement\Notifications;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;

use Illuminate\Notifications\Messages\MailMessage;

class CreateSectionNotification extends BaseNotification {
    use Queueable;

    protected $user_id;

    protected $section;
    /**
     * Create a new notification instance.
     */
    public function __construct($section, $user_id) {
        $this->user_id = $user_id;
        $this->section = $section;
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
        $titleEn = $this->section->title['en'] ?? $this->section->title;
        $titleAr = $this->section->title['ar'] ?? $this->section->title;
        return (new MailMessage)
            ->line('Make New Section')
            ->line('Section Title (EN): ' . $titleEn)
            ->line('عنوان القسم (AR): ' . $titleAr)
            ->line('Published: ' . ($this->section->is_published ? "True" : "False"))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array {
        return [
            'user_id' => $this->user_id,
            'data' => $this->section,
            'type' => static::class,
        ];
    }
}
