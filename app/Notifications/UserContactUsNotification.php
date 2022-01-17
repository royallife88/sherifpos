<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserContactUsNotification extends Notification
{
    use Queueable;
    protected $data;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
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
        $data = $this->data;

        $mail = (new MailMessage)
            ->from($data['from'], $data['site_title'])
            ->subject($data['subject'])
            ->view(
                'notification_template.plain_html',
                ['content' => $data['email_body']]
            );

        if (isset($data['files'])) {
            foreach ($data['files'] as $file) {
                $attachment =  $file;
                $attachment_name = !empty($file) ? $file->getClientOriginalName() : null;
                if (!empty($attachment) && $attachment_name) {
                    $mail->attach($attachment, ['as' => $attachment_name]);
                }
            }
        }

        return $mail;
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
