<?php

namespace App\Notifications;

use App\Models\Budget; // 1. Import Budget
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BudgetStatusUpdated extends Notification
{
    use Queueable;

    // 2. Add properties
    protected $budget;
    protected $message;
    protected $actionText;
    protected $actionUrl;

    /**
     * Create a new notification instance.
     */
    // 3. Update the constructor
    public function __construct(Budget $budget, string $message, string $actionText)
    {
        $this->budget = $budget;
        $this->message = $message;
        $this->actionText = $actionText;
        $this->actionUrl = route('budgets.index'); // Link back to the budget list
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail']; // We'll just use email for now
    }

    /**
     * Get the mail representation of the notification.
     */
    // 4. Update the toMail method
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Your Budget Submission Status')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line($this->message) // e.g., "Your budget submission for Marketing has been approved."
                    ->line('Total Amount: $' . number_format($this->budget->total_amount, 2))
                    ->action($this->actionText, $this->actionUrl)
                    ->line('Thank you for using our application!');
    }
}