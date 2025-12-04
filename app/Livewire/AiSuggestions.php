<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Lazy; // [1] Crucial for lazy loading
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Budget;
use App\Models\Expense;

#[Lazy] // [2] This tells Livewire to defer loading this component
class AiSuggestions extends Component
{
    public $budgetId;
    public $suggestions = [];
    public $loaded = false;

    public function mount($budgetId = null)
    {
        $this->budgetId = $budgetId;
    }

    // [3] This runs automatically when the component lazy loads
    public function render()
    {
        if ($this->budgetId) {
            $this->fetchAiSuggestions();
        }

        return view('livewire.ai-suggestions');
    }

    public function fetchAiSuggestions()
    {
        $user = Auth::user();
        $budget = Budget::find($this->budgetId);

        if (!$budget) {
            return;
        }

        // Fetch expenses specifically for the AI context
        // We re-query here so this component is self-contained
        $expenses = Expense::where('budget_id', $budget->id)
            ->where('user_id', $user->id)
            ->latest()
            ->take(20) // Limit context to recent items to save tokens/bandwidth
            ->get();

        $aiServiceUrl = config('services.ai_suggester.url');

        if ($aiServiceUrl) {
            try {
                // Prepare data for the AI (Same logic as your Controller)
                $budgetData = [
                    'total_budget' => $budget->total_amount ?? 0,
                    'spent' => $expenses->where('status', 'approved')->sum('amount'),
                ];

                $expenseData = $expenses->map(fn($exp) => [
                    'category' => $exp->category,
                    'amount' => $exp->amount,
                    'status' => $exp->status,
                    'date' => $exp->created_at->toIso8601String(),
                ]);

                $response = Http::timeout(15)->post($aiServiceUrl, [
                    'user_id' => $user->id,
                    'budget' => $budgetData,
                    'expenses' => $expenseData,
                ]);

                if ($response->successful()) {
                    $text = trim($response->body());
                    if (!empty($text) && strcasecmp($text, 'All clear.') !== 0) {
                        $this->suggestions[] = $text;
                    }
                }
            } catch (\Exception $e) {
                Log::error('AI Suggestion Service failed: ' . $e->getMessage());
                $this->suggestions[] = "AI service temporarily unavailable.";
            }
        }
    }

    // [4] Placeholder shown while waiting for the AI response
    public function placeholder()
    {
        return view('livewire.placeholders.ai-loading');
    }
}