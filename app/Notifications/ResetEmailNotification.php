<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;


class ResetEmailNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $resetHistory)
    {
        $this->user = $user;
        $this->resetData = $resetHistory;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        return (new MailMessage)
            ->greeting("Halo, " . $this->user->name)
            ->line(new HtmlString('<p style="color:black">"Adam mengingkari, maka anak cucunya pun mengingkari. Adam dijadikan lupa, maka anak cucunya dijadikan lupa; dan Adam berbuat salah, maka anak cucunya berbuat salah. (HR. Tirmidzi)”'))
            ->line(new HtmlString('<p style="color:black">Begitulah, manusia memiliki tabiat pelupa. Bahkan ada yang menyebutkan bahwa manusia disebut insan, karena sifat nisyan (pelupa) yang melekat padanya.</p>'))
            ->line(new HtmlString('<p style="color:black">Berikut Kode Reset Email kamu :</p><p style="text-align:center; color:white; font-size:25px; background-color:gray;" >' . $this->resetData->code . '<center>'))
            ->line(new HtmlString('<p style="color:black text-align:center">Keep Halal Brother and Sister</p>'));
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
