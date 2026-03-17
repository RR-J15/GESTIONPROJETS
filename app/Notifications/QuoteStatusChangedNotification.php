<?php

namespace App\Notifications;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class QuoteStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(public Quote $quote) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Le devis "' . $this->quote->reference . '" est passé au statut : ' . ucfirst($this->quote->status->value),
            'url'     => route('quotes.show', $this->quote),
            'type'    => 'quote_status_changed',
        ];
    }
}