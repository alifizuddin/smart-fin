<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Expense;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SubmitExpense extends Component
{
    use WithFileUploads;

    public $amount;
    public $description;
    public $transaction_date;
    public $category;
    public $receipt; 
    
    // State variables for modals
    public $showModal = false;
    public $confirmingExpenseSubmission = false;
    public $isProcessing = false;

    protected $rules = [
        'amount' => 'required|numeric|min:0.01',
        'description' => 'nullable|string|max:255',
        'category' => 'required|string|max:100',
        'transaction_date' => 'required|date',
        'receipt' => 'required|image|max:10240',
    ];

    public function updatedReceipt()
    {
        $this->validateOnly('receipt');
        $this->isProcessing = true;

        try {
            $filePath = $this->receipt->getRealPath();
            $fileName = $this->receipt->getClientOriginalName();

            // Send to N8n Webhook
            $response = Http::attach(
                'receipt', file_get_contents($filePath), $fileName
            )->post('http://n8n:5678/webhook/receipt_process');

            if ($response->successful()) {
                $data = $response->json();
                
                // 1. Extract Amount
                if (isset($data['data']['amount']) && is_numeric($data['data']['amount'])) {
                    $this->amount = $data['data']['amount'];
                }

                // 2. Extract Description
                if (isset($data['description'])) {
                    $this->description = $data['description'];
                }

                // 3. Extract Category
                $this->category = $data['category'] ?? 'Uncategorized';

                // 4. Extract Date
                $extractedDate = $data['data']['date'] ?? null;
                $this->transaction_date = $extractedDate 
                    ? Carbon::parse($extractedDate)->format('Y-m-d') 
                    : now()->format('Y-m-d');

                // [UPDATED] Open the modal instead of showing inline form
                $this->confirmingExpenseSubmission = true; 

            } else {
                Log::error('N8N processing error: ' . $response->body());
                // Fallback: Open modal with empty/default data so user can fill it manually
                $this->transaction_date = now()->format('Y-m-d');
                $this->category = 'Uncategorized';
                $this->confirmingExpenseSubmission = true;
            }

        } catch (\Exception $e) {
            Log::error('Could not connect to N8N: ' . $e->getMessage());
            $this->confirmingExpenseSubmission = true; // Fallback
        } finally {
            $this->isProcessing = false;
        }
    }

    public function save()
    {
        $this->validate();

        $path = $this->receipt->store('receipts', 'public');

        Expense::create([
            'user_id' => auth()->id(),
            'department_id' => auth()->user()->department_id ?? 1, 
            'amount' => $this->amount,
            'category' => $this->category,
            'description' => $this->description,
            'receipt_path' => $path,
            'status' => 'pending',
            'transaction_date' => $this->transaction_date, 
        ]);

        // Reset everything and close modal
        $this->reset(['amount', 'description', 'receipt', 'transaction_date', 'category', 'confirmingExpenseSubmission']);
        
        // Close the main entry modal (if you are using one) or just flash message
        $this->dispatch('expense-created', message: 'Expense submitted successfully!');
    }

    public function render()
    {
        return view('livewire.submit-expense');
    }
}