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
    public $category; // [NEW] To store the AI-detected category
    public $receipt; 
    
    public $showModal = false;
    public $isProcessing = false;
    public $isReviewing = false; 

    protected $rules = [
        'amount' => 'required|numeric|min:0.01',
        'description' => 'nullable|string|max:255',
        'category' => 'required|string|max:100', // [NEW] Validate category
        'transaction_date' => 'required|date',
        'receipt' => 'required|image|max:10240',
    ];

    public function updatedReceipt()
    {
        $this->validateOnly('receipt');
        $this->isProcessing = true;
        $this->isReviewing = false;

        try {
            $filePath = $this->receipt->getRealPath();
            $fileName = $this->receipt->getClientOriginalName();

            // [UPDATED] Use N8N Webhook URL
            $response = Http::attach(
                'receipt', file_get_contents($filePath), $fileName
            )->post('http://n8n:5678/webhook/process-receipt');

            if ($response->successful()) {
                $data = $response->json();
                
                // 1. Amount
                if (isset($data['data']['amount']) && is_numeric($data['data']['amount'])) {
                    $this->amount = $data['data']['amount'];
                }

                // 2. Description
                if (isset($data['description'])) {
                    $this->description = $data['description'];
                }

                // 3. Category (Aligning with ExpenseController logic)
                $this->category = $data['category'] ?? 'Uncategorized';

                // 4. Date
                $extractedDate = $data['data']['date'] ?? null;
                $this->transaction_date = $extractedDate 
                    ? Carbon::parse($extractedDate)->format('Y-m-d') 
                    : now()->format('Y-m-d');

                // Show the review section
                $this->isReviewing = true; 

            } else {
                Log::error('N8N processing error: ' . $response->body());
                // Fallback: still show the form so user can enter data manually
                $this->isReviewing = true;
                $this->transaction_date = now()->format('Y-m-d');
                $this->category = 'Uncategorized';
            }

        } catch (\Exception $e) {
            Log::error('Could not connect to N8N: ' . $e->getMessage());
            $this->isReviewing = true; // Fallback to manual entry
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
            'category' => $this->category, // [UPDATED] Use the variable
            'description' => $this->description,
            'receipt_path' => $path,
            'status' => 'pending',
            'transaction_date' => $this->transaction_date, 
        ]);

        $this->reset(['amount', 'description', 'receipt', 'transaction_date', 'category', 'isReviewing']);
        $this->showModal = false;

        $this->dispatch('expense-created', message: 'Expense submitted successfully!');
    }

    public function render()
    {
        return view('livewire.submit-expense');
    }
}