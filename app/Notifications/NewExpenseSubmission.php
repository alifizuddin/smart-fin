<?php

namespace App\Notifications;

use App\Models\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewExpenseSubmission extends Notification implements ShouldQueue
{
    use Queueable;

    public $expense;

    /**
     * Create a new notification instance.
     */
    public function __construct(Expense $expense)
    {
        $this->expense = $expense;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Expense Pending Approval')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new expense has been submitted by ' . $this->expense->user->name . '.')
            ->line('Amount: $' . number_format($this->expense->amount, 2))
            ->line('Category: ' . $this->expense->category)
            ->action('Review Expense', route('expenses.approvals.index')) // Ensure this route exists
            ->line('Please review this request at your earliest convenience.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'New Expense Submission',
            'message' => sprintf(
                'New expense of $%s submitted by %s for %s',
                number_format($this->expense->amount, 2),
                $this->expense->user->name,
                $this->expense->category
            ),
            'amount' => $this->expense->amount,
            'expense_id' => $this->expense->id,
            'action_url' => route('expenses.approvals.index'),
            'type' => 'expense_submission',
            'user_name' => $this->expense->user->name,
            'created_at' => now()->toDateTimeString(),
        ];
    }
}