<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Expense;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SubmitExpense extends Component
{
    use WithFileUploads;

    public $amount;
    public $description;
    public $receipt; 
    public $showModal = false;
    public $isProcessing = false; // Controls the "Processing..." UI

    protected $rules = [
        'amount' => 'required|numeric|min:0.01',
        'description' => 'nullable|string|max:255',
        'receipt' => 'required|image|max:10240', // 10MB Max
    ];

    // This function runs automatically as soon as a file is selected
    public function updatedReceipt()
    {
        $this->validateOnly('receipt');
        $this->isProcessing = true; // Turn on loading state

        try {
            // 1. Prepare the file for sending
            $filePath = $this->receipt->getRealPath();
            $fileName = $this->receipt->getClientOriginalName();

            // 2. Send to Python API (Note: 'python_api' is the docker service name)
            $response = Http::attach(
                'receipt', file_get_contents($filePath), $fileName
            )->post('http://python_api:5010/process-receipt');

            // 3. Handle the response
            if ($response->successful()) {
                $data = $response->json();
                
                // Auto-fill amount if found
                if (isset($data['data']['amount']) && $data['data']['amount']) {
                    $this->amount = $data['data']['amount'];
                }

                // Auto-fill description/summary if found
                if (isset($data['description']) && $data['description']) {
                    $this->description = $data['description'];
                }
            } else {
                Log::error('Receipt processing error: ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('Could not connect to Python API: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false; // Turn off loading state
        }
    }

    public function save()
{
    $this->validate();

    // Store the file
    $path = $this->receipt->store('receipts', 'public');

    // Create the expense record with ALL required fields
    Expense::create([
        'user_id' => auth()->id(),
        // [FIX 1] Get department from the authenticated user
        'department_id' => auth()->user()->department_id ?? 1, 
        
        'amount' => $this->amount,
        
        // [FIX 2] Provide a default category (since the form doesn't have a dropdown yet)
        'category' => 'Uncategorized', 
        
        'description' => $this->description,
        'receipt_path' => $path,
        'status' => 'pending',
        
        // [FIX 3] Use the correct column name 'transaction_date' instead of 'date'
        'transaction_date' => now(), 
    ]);

    // Reset form and close modal
    $this->reset(['amount', 'description', 'receipt']);
    $this->showModal = false;

    $this->dispatch('expense-created', message: 'Expense submitted successfully!');
}
    public function render()
    {
        return view('livewire.submit-expense');
    }
}