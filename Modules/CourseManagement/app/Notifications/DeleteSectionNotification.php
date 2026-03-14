<?php

namespace Modules\CourseManagement\Notifications;

use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DeleteSectionNotification extends BaseNotification
{
    use Queueable;

    public $data;


    public $user_id;
    /**
     * Create a new notification instance.
     */
    public function __construct($data,$user_id) {
        $this->user_id=$user_id;
        $this->data=$data;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $titleAr = $this->data['title']['ar'] ?? null;
        $titleEn = $this->data['title']['en'] ?? null;
        return (new MailMessage)
            ->subject('Section Deleted')
            ->line('A section has been deleted from the platform.')
            ->line('Section Title (EN): ' . $titleEn)
            ->line('عنوان القسم (AR): ' . $titleAr)
            ->line('Published: ' . ($this->data['is_published'] ? "True" : "False"))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'user_id'=>$this->user_id,
            'section'=>$this->data,
            'type'=>static::class
        ];
    }
}
