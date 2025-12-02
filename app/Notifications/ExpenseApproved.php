<?php

namespace App\Notifications;

use App\Models\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;


class ExpenseApproved extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The expense instance.
     *
     * @var \App\Models\Expense
     */
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
     *
     * @return array<int, string>
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
            ->subject('✅ Expense Approved - ' . $this->expense->category)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Great news! Your expense submission has been approved.')
            ->line('**Expense Details:**')
            ->line('Amount: **$' . number_format($this->expense->amount, 2) . '**')
            ->line('Category: **' . ucfirst($this->expense->category) . '**')
            ->line('Date: **' . \Carbon\Carbon::parse($this->expense->transaction_date)->format('M d, Y') . '**')
            ->line('Description: ' . ($this->expense->description ?: 'No description provided'))
            ->action('View Expense Details', $this->getExpenseUrl())
            ->line('If you have any questions, please contact your manager.')
            ->line('Thank you for using SmartFin360!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        try {
            $formattedDate = \Carbon\Carbon::parse($this->expense->transaction_date)->format('M d, Y');
            
            return [
                'title' => 'Expense Approved',
                'message' => sprintf(
                    'Your %s expense of $%s from %s has been approved.',
                    $this->expense->category,
                    number_format($this->expense->amount, 2),
                    $formattedDate
                ),
                'amount' => $this->expense->amount,
                'expense_id' => $this->expense->id,
                'status' => 'approved',
                'action_url' => $this->getExpenseUrl(),
                'type' => 'expense_approved',
                'created_at' => now()->toDateTimeString(),
            ];
        } catch (\Exception $e) {
            \Log::error('Error formatting notification data', [
                'error' => $e->getMessage(),
                'expense_id' => $this->expense->id,
                'transaction_date' => $this->expense->transaction_date
            ]);
            
            // Fallback message if date parsing fails
            return [
                'title' => 'Expense Approved',
                'message' => sprintf(
                    'Your %s expense of $%s has been approved.',
                    $this->expense->category,
                    number_format($this->expense->amount, 2)
                ),
                'amount' => $this->expense->amount,
                'expense_id' => $this->expense->id,
                'status' => 'approved',
                'action_url' => $this->getExpenseUrl(),
                'type' => 'expense_approved',
                'created_at' => now()->toDateTimeString(),
            ];
        }
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Get the URL for the expense.
     */
    protected function getExpenseUrl(): string
    {
        return route('expenses.my-expenses', ['expense' => $this->expense->id]);
    }
}
