<?php

namespace App\Listeners;

use App\Events\ExpenseCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendExpenseToN8n implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ExpenseCreated $event): void
    {
        // Replace with your actual N8N Webhook URL
        $n8nWebhookUrl = env('N8N_AUDIT_WEBHOOK_URL'); 

        if ($n8nWebhookUrl) {
            try {
                Http::post($n8nWebhookUrl, [
                    'expense_id' => $event->expense->id,
                    'amount' => $event->expense->amount,
                    'date' => $event->expense->transaction_date, // Y-m-d
                    'created_at' => $event->expense->created_at->toDateTimeString(),
                    'category' => $event->expense->category,
                    'description' => $event->expense->description,
                    'user_id' => $event->expense->user_id,
                    'user_name' => $event->expense->user->name,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send expense to n8n: ' . $e->getMessage());
            }
        }
    }
}