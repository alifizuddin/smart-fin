<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads; // [1] Needed for file uploads
use App\Models\Expense; // Assuming this model exists based on your files

class SubmitExpense extends Component
{
    use WithFileUploads;

    public $amount;
    public $description;
    public $receipt; // This will handle the file temporary upload
    public $showModal = false; // We move the modal state here

    // Validation rules
    protected $rules = [
        'amount' => 'required|numeric|min:0.01',
        'description' => 'nullable|string|max:255',
        'receipt' => 'required|image|max:10240', // 10MB max
    ];

    public function save()
    {
        $this->validate();

        // Save logic (similar to your Controller)
        $path = $this->receipt->store('receipts', 'public');

        Expense::create([
            'user_id' => auth()->id(),
            'amount' => $this->amount,
            'description' => $this->description,
            'receipt_path' => $path,
            'status' => 'pending',
            'date' => now(),
        ]);

        // Reset form and close modal
        $this->reset(['amount', 'description', 'receipt']);
        $this->showModal = false;

        // Dispatch browser event to show success message (optional)
        $this->dispatch('expense-created', message: 'Expense submitted successfully!');
        
        // If you want to refresh the parent dashboard's list, you can emit an event
        // $this->dispatch('refresh-dashboard'); 
    }

    public function render()
    {
        return view('livewire.submit-expense');
    }
}