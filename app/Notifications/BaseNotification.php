<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

abstract class BaseNotification extends Notification implements ShouldQueue {
    use Queueable;

    protected array $data;
    protected int $user_id;

    public function __construct(array $data, int $user_id) {
        $this->data = $data;
        $this->user_id = $user_id;
    }

    public function via(object $notifiable): array {
        return ['mail', 'database'];
    }


    abstract public function toMail(object $notifiable): MailMessage;

    abstract public function toArray(object $notifiable): array;
}
