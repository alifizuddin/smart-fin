<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\BudgetService;
use Livewire\Attributes\On;

class BudgetCharts extends Component
{
    public $departmentData = [];
    public $categoryData = [];
    public $departmentLabels = [];
    public $pieChartData = []; 

    protected $budgetService;
    protected $listeners = ['refresh' => '$refresh', 'charts-data-loaded'];


    public function __construct()
    {
        $this->budgetService = app(BudgetService::class);
    }

    public function mount()
    {
        $this->loadChartData();
    }
    public function updated($property)
    {
        if (in_array($property, ['status', 'department_id', 'start_date', 'end_date'])) {
            $this->resetPage();
            $this->emit('filters-updated');
        }
    }

    #[On('filters-updated')]
    public function loadChartData()
    {
        $request = request();
        $allFilteredBudgets = $this->budgetService->getAllFilteredBudgets($request);
        
        // Get department data
        $chartData = $this->budgetService->getBudgetChartData($allFilteredBudgets);
        
        // Initialize data structures
        $this->departmentData = [
            'budgets' => [],
        'used' => []
    ];
    $this->departmentLabels = [];
    
    if (!empty($chartData['bar'])) {
        $this->departmentData = [
            'budgets' => $chartData['bar']->map->total->toArray(),
            'used' => $chartData['bar']->map->used->toArray()
        ];
        $this->departmentLabels = array_keys($this->departmentData['budgets']);
    }
    
    // For admin pie chart
    $this->pieChartData = $chartData['pie'] ?? [];
    
    // Get category data if not admin
    if (!auth()->user()->hasRole('Admin')) {
        $budgetIds = $allFilteredBudgets->pluck('id');
        $this->categoryData = \App\Models\Expense::whereIn('budget_id', $budgetIds)
            ->where('status', 'approved')
            ->selectRaw('category, sum(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();
    }
    
    $this->dispatch('charts-data-loaded');
}
    public function render()
    {
        return view('livewire.budget-charts');
    }
}