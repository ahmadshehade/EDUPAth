<?php

namespace Modules\CourseManagement\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CourseUpdateNotification extends Notification {
    use Queueable;

    protected $user_id;

    protected $course;
    /**
     * Create a new notification instance.
     */
    public function __construct($course, $user_id) {
        $this->user_id = $user_id;
        $this->course = $course;
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
        $titleEn = $this->course->title['en'] ?? $this->course->title; // fallback
        $titleAr = $this->course->title['ar'] ?? $this->course->title;

        return (new MailMessage)
            ->line(' Update  Course')
            ->line('Course Title (EN): ' . $titleEn)
            ->line('عنوان الدورة (AR): ' . $titleAr)
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array {
        return [
            'user_id' => $this->user_id,
            'data' => $this->course,
            'type' => static::class,
        ];
    }
}
