<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportTicketSubmitted extends Notification
{
    use Queueable;

    public function __construct(public SupportTicket $ticket) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your FursGo support request '.$this->ticket->ticket_number)
            ->greeting('Thanks for contacting FursGo.')
            ->line('We have received your request and our team will follow up by email.')
            ->line('Reference: '.$this->ticket->ticket_number)
            ->line('Subject: '.$this->ticket->subject)
            ->line('Responses are usually within 24 hours.');
    }
}
