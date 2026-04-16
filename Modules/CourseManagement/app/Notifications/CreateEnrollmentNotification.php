<?php

namespace Modules\CourseManagement\Notifications;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;

use Illuminate\Notifications\Messages\MailMessage;

class CreateEnrollmentNotification extends BaseNotification
{
    use Queueable;

    public $user_id;
    public $enrollment;

    /**
     * Create a new notification instance.
     */
    public function __construct($user_id, $enrollment)
    {
        $this->user_id = $user_id;
        $this->enrollment = $enrollment;
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
        $courseName = $this->enrollment->course?->title ?? 'Course';
        $userName   = $this->enrollment->user?->name ?? 'User';
        $status     = $this->enrollment->status?->value ?? 'N/A';
        $progress   = $this->enrollment->progress ?? 0;
        $enrolledAt = $this->enrollment->enrolled_at ?? now();

        return (new MailMessage)
            ->subject('🎓 Enrollment Confirmation - ' . $courseName)
            ->greeting('Hello ' . $userName . ',')

            ->line('You have been successfully enrolled in the following course:')
            ->line("📘 Course: {$courseName}")

            ->line("📅 Enrolled At: {$enrolledAt}")
            ->line("📊 Progress: {$progress}%")
            ->line("📌 Status: {$status}")

            ->action('Go to Course', url('/courses/' . $this->enrollment->course_id))

            ->line('We wish you a great learning experience 🚀')
            ->salutation('Best regards, ' . config('app.name'));
    }
    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'user_id' => $this->user_id,
            'enrollment' => $this->enrollment,
            'type' => self::class
        ];
    }
}
