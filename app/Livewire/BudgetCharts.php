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

    protected $listeners = [
        'refresh' => '$refresh',
        'filters-updated' => 'loadChartData',
        'charts-data-loaded' => '$refresh'
    ];

    #[On('filters-updated')]
    public function loadChartData()
{
    try {
        $this->resetChartData();
        
        $request = request();
        $allFilteredBudgets = $this->budgetService->getAllFilteredBudgets($request);
        $chartData = $this->budgetService->getBudgetChartData($allFilteredBudgets);
        
        if (auth()->user()->hasAnyRole(['Admin', 'Finance Admin'])) {
            $this->handleAdminView($chartData);
        } else {
            $this->handleDepartmentView($allFilteredBudgets, $chartData);
        }
        
        // Ensure we have valid data before dispatching
        if ($this->hasValidChartData()) {
            $this->dispatch('charts-data-loaded');
        }
    } catch (\Exception $e) {
        \Log::error('Error loading chart data: ' . $e->getMessage());
        $this->dispatch('chart-error', ['message' => 'Failed to load chart data. Please try again.']);
    }
}

protected function resetChartData(): void
{
    $this->departmentData = ['budgets' => [], 'used' => []];
    $this->departmentLabels = [];
    $this->pieChartData = [];
    $this->categoryData = [];
}

protected function handleAdminView($chartData): void
{
    $this->pieChartData = $chartData['pie'] ?? [];
    
    if (isset($chartData['bar']) && !$chartData['bar']->isEmpty()) {
        $this->departmentData = [
            'budgets' => $chartData['bar']->map->total->toArray(),
            'used' => $chartData['bar']->map->used->toArray()
        ];
        $this->departmentLabels = array_keys($this->departmentData['budgets']);
    }
}

protected function handleDepartmentView($allFilteredBudgets, $chartData): void
{
    $budgetIds = $allFilteredBudgets->pluck('id');
    
    $this->categoryData = \App\Models\Expense::query()
        ->whereIn('budget_id', $budgetIds)
        ->where('status', 'approved')
        ->selectRaw('category, sum(amount) as total')
        ->groupBy('category')
        ->pluck('total', 'category')
        ->toArray();
    
    if (isset($chartData['bar']) && !$chartData['bar']->isEmpty()) {
        $this->departmentData = [
            'budgets' => $chartData['bar']->map->total->toArray(),
            'used' => $chartData['bar']->map->used->toArray()
        ];
        $this->departmentLabels = array_keys($this->departmentData['budgets']);
    }
}

protected function hasValidChartData(): bool
{
    if (auth()->user()->hasAnyRole(['Admin', 'Finance Admin'])) {
        return !empty($this->pieChartData) || !empty($this->departmentData['budgets']);
    }
    
    return !empty($this->categoryData) || !empty($this->departmentData['budgets']);
}
    public function render()
    {
        // Debug information
        if (app()->environment('local')) {
            \Log::debug('BudgetCharts render', [
                'user_roles' => auth()->user()->getRoleNames(),
                'has_admin_role' => auth()->user()->hasAnyRole(['Admin', 'Finance Admin']),
                'pie_chart_data' => $this->pieChartData,
                'department_data' => $this->departmentData,
                'category_data' => $this->categoryData,
                'department_labels' => $this->departmentLabels,
            ]);
        }
        
        return view('livewire.budget-charts');
    }
}