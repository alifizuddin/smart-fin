<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewAiAlert extends Notification
{
    use Queueable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database']; // Store in the 'notifications' table
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'ai_anomaly',
            'message' => $this->data['message'],
            'severity' => $this->data['severity'], // info, warning, critical
            'details' => $this->data['details'] ?? null,
            'created_at' => now(),
        ];
    }
}