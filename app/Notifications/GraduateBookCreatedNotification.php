<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class GraduateBookCreatedNotification extends Notification
{
    use Queueable;

    public $graduateBookCreated;
    public $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($graduateBookCreated,$user)
    {
        $this->graduateBookCreated = $graduateBookCreated;
        $this->user = $user;
    }

   /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }
    
    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [

            'graduateBookCreated' => $this->graduateBookCreated,
            'user' => $this->user
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
