<?php

namespace App\Notifications;

use App\Models\Plat;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;


class SendMail extends Notification
{
    use Queueable;

    private $plat;



    /**
     * Create a new notification instance.
     */
    public function __construct(Plat $plat)
    {
        $this->plat = $plat;


    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {

        return (new MailMessage)
            ->line( "Salut, ton plat a bien été enregistré " . Auth::user()->name)
            ->line('Title: ' . $this->plat->titre)
            ->action('Liste PLats', url('/plats/'. $this->plat->id))
            ->line('Thanksssssssssssssssssssssssssssssssss');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [

        ];
    }
}
