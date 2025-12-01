<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Budgets Management') }}
            </h2>
            <div class="flex items-center gap-3">
                @hasanyrole('Admin|Manager')
                    <a href="{{ route('budgets.report', request()->query()) }}"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Report
                    </a>
                    <a href="{{ route('budgets.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Budget
                    </a>
                @endhasanyrole
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/30 text-blue-800 dark:text-blue-300 p-5 rounded-xl shadow-sm relative overflow-hidden">
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-sm font-medium text-blue-700 dark:text-blue-400">Total Budget</p>
                            <p class="text-2xl font-bold mt-1">${{ number_format($summary['totalBudget'], 2) }}</p>
                        </div>
                        <div class="bg-white/60 dark:bg-white/10 p-2 rounded-full text-blue-800 dark:text-blue-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800/30 text-indigo-800 dark:text-indigo-300 p-5 rounded-xl shadow-sm relative overflow-hidden">
                     <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-sm font-medium text-indigo-700 dark:text-indigo-400">Total Spent</p>
                            <p class="text-2xl font-bold mt-1">${{ number_format($summary['totalSpent'], 2) }}</p>
                        </div>
                        <div class="bg-white/60 dark:bg-white/10 p-2 rounded-full text-indigo-800 dark:text-indigo-300">
                           <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800/30 text-green-800 dark:text-green-300 p-5 rounded-xl shadow-sm relative overflow-hidden">
                     <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-sm font-medium text-green-700 dark:text-green-400">Remaining Funds</p>
                            <p class="text-2xl font-bold mt-1 {{ $summary['remainingFunds'] < 0 ? 'text-red-600' : '' }}">${{ number_format($summary['remainingFunds'], 2) }}</p>
                        </div>
                        <div class="bg-white/60 dark:bg-white/10 p-2 rounded-full text-green-800 dark:text-green-300">
                           <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/30 text-red-800 dark:text-red-300 p-5 rounded-xl shadow-sm relative overflow-hidden">
                    <div class="flex justify-between items-start relative z-10">
                        <div>
                            <p class="text-sm font-medium text-red-700 dark:text-red-400">Over-Budget Depts</p>
                            <p class="text-2xl font-bold mt-1">{{ $summary['overBudgetCount'] }}</p>
                        </div>
                        <div class="bg-white/60 dark:bg-white/10 p-2 rounded-full text-red-800 dark:text-red-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            @hasrole('Admin')
                                Budget Distribution
                            @else
                                Spending By Category
                            @endhasrole
                        </h3>
                        <div class="relative h-64">
                            @hasrole('Admin')
                                <canvas id="departmentPieChart"></canvas>
                            @else
                                <canvas id="categoryDoughnutChart"></canvas>
                            @endhasrole
                        </div>
                    </div>
                    <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-sm rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Spending vs Budget</h3>
                        <div class="relative" style="height: 350px">
                            <canvas id="departmentBarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 p-5">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">All Budgets</h3>
                    
                    @hasrole('Admin')
                    <form action="{{ route('budgets.bulkAction') }}" method="POST" id="bulk-action-form" class="flex items-center">
                        @csrf
                        <div class="relative">
                            <button id="dropdownActionButton" data-dropdown-toggle="dropdownAction" type="button"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-100 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                                <span class="sr-only">Action button</span>
                                Bulk Actions
                                <svg class="w-2.5 h-2.5 ml-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                </svg>
                            </button>
                            <div id="dropdownAction" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 dark:divide-gray-600">
                                <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownActionButton">
                                    <li>
                                        <button type="submit" name="action" value="delete" class="block w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white text-red-600">Delete Selected</button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </form>
                    @endhasrole
                </div>

                <form action="{{ route('budgets.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @role('Admin')
                        <div>
                            <select name="department_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                                <option value="">All Departments</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" @selected(($filters['department_id'] ?? '') == $department->id)>{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endrole
                    <div>
                        <select name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                            <option value="">All Statuses</option>
                            <option value="active" @selected(($filters['status'] ?? '') == 'active')>Active</option>
                            <option value="closed" @selected(($filters['status'] ?? '') == 'closed')>Closed</option>
                            <option value="pending" @selected(($filters['status'] ?? '') == 'pending')>Pending</option>
                        </select>
                    </div>
                    <div>
                         <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                </svg>
                            </div>
                            <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Start Date">
                        </div>
                    </div>
                    <div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                                </svg>
                            </div>
                            <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="End Date">
                        </div>
                    </div>
                    <div class="flex">
                        <button type="submit" class="w-full text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:focus:ring-indigo-800">Filter</button>
                    </div>
                </form>

                <div class="mt-6 overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                @hasrole('Admin')
                                <th scope="col" class="p-4">
                                    <div class="flex items-center">
                                        <input id="checkbox-all" type="checkbox" class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="checkbox-all" class="sr-only">checkbox</label>
                                    </div>
                                </th>
                                @endhasrole
                                <th scope="col" class="px-6 py-3 cursor-pointer hover:text-indigo-600" onclick="window.location.href='{{ route('budgets.index', array_merge($filters, ['sort_by' => 'department', 'sort_order' => ($filters['sort_by'] ?? '') == 'department' && ($filters['sort_order'] ?? '') == 'asc' ? 'desc' : 'asc'])) }}'">
                                    Department
                                </th>
                                <th scope="col" class="px-6 py-3 cursor-pointer hover:text-indigo-600" onclick="window.location.href='{{ route('budgets.index', array_merge($filters, ['sort_by' => 'total_amount', 'sort_order' => ($filters['sort_by'] ?? '') == 'total_amount' && ($filters['sort_order'] ?? '') == 'asc' ? 'desc' : 'asc'])) }}'">
                                    Total Amount
                                </th>
                                <th scope="col" class="px-6 py-3">Usage</th>
                                <th scope="col" class="px-6 py-3">Budget Name</th>
                                <th scope="col" class="px-6 py-3">Creator</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($budgets as $budget)
                                @php
                                    $usedAmount = $budget->expenses->where('status', 'approved')->sum('amount');
                                    $percentage = $budget->total_amount > 0 ? round(($usedAmount / $budget->total_amount) * 100, 1) : 0;
                                    $remaining = $budget->total_amount - $usedAmount;
                                    $barColor = $percentage < 70 ? 'bg-green-500' : ($percentage < 90 ? 'bg-yellow-400' : 'bg-red-600');
                                @endphp
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    @hasrole('Admin')
                                    <td class="w-4 p-4">
                                        <div class="flex items-center">
                                            <input type="checkbox" name="budgets[]" value="{{ $budget->id }}" form="bulk-action-form" class="budget-checkbox w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        </div>
                                    </td>
                                    @endhasrole
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $budget->department->name }}</td>
                                    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">${{ number_format($budget->total_amount, 2) }}</td>
                                    <td class="px-6 py-4 w-64">
                                        <div class="space-y-2">
                                            <div class="flex justify-between items-center">
                                                <span class="text-xs font-semibold {{ $percentage >= 90 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-300' }}">
                                                    {{ min($percentage, 100) }}% Used
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    ${{ number_format($usedAmount, 0) }} of ${{ number_format($budget->total_amount, 0) }}
                                                </span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 overflow-hidden">
                                                <div class="h-full rounded-full transition-all duration-500 {{ $barColor }}" 
                                                    style="width: {{ min($percentage, 100) }}%;">
                                                </div>
                                            </div>
                                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                                <span>$0</span>
                                                <span>${{ number_format($budget->total_amount, 0) }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">{{ $budget->name }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="h-6 w-6 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-xs font-bold text-indigo-600 dark:text-indigo-400 mr-2">
                                                {{ substr($budget->creator->name ?? '?', 0, 1) }}
                                            </div>
                                            {{ $budget->creator->name ?? 'Unknown' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusStyles = [
                                                'active' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                'closed' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                                'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                            ];
                                        @endphp
                                        <span class="{{ $statusStyles[$budget->status] ?? 'bg-gray-100 text-gray-800' }} text-xs font-medium px-2.5 py-0.5 rounded border border-transparent">
                                            {{ ucfirst($budget->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button data-budget-id="{{ $budget->id }}" class="view-budget-btn text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors mr-2" title="View">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        
                                        @role('Admin')
                                            @if($budget->status != 'pending')
                                            <button data-budget-id="{{ $budget->id }}" class="edit-budget-btn text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors mr-2" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            @endif

                                            @if($budget->status == 'pending')
                                                <form action="{{ route('budgets.approve', $budget) }}" method="POST" class="inline-block">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="text-green-500 hover:text-green-700 transition-colors mr-2" title="Approve">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    </button>
                                                </form>
                                                <form action="{{ route('budgets.reject', $budget) }}" method="POST" class="inline-block" onsubmit="return confirm('Reject budget?');">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="text-red-500 hover:text-red-700 transition-colors" title="Reject">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        @endrole
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400 italic">
                                        No budgets found matching criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $budgets->links() }}
                </div>
            </div>
        </div>
    </div>

    <div id="viewBudgetModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="relative w-3/4 max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="flex justify-between items-center p-6 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Budget Details</h3>
                <button type="button" data-modal-close class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="modal-body-content" class="p-6"></div>
            <div class="p-6 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex justify-end">
                <button type="button" data-modal-close class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">Close</button>
            </div>
        </div>
    </div>

    <div id="editBudgetModal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="relative w-full max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="flex justify-between items-center p-6 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit Budget</h3>
                <button type="button" data-modal-close class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6">
                <div id="edit-modal-loading" class="hidden flex justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-4 border-indigo-600 border-t-transparent"></div>
                </div>
                <form id="editBudgetForm" method="POST" class="space-y-4">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Budget Name</label>
                            <input type="text" name="name" id="edit-name" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Department</label>
                            <select name="department_id" id="edit-department_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Total Amount</label>
                            <input type="number" name="total_amount" id="edit-total_amount" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                            <input type="date" name="period_start" id="edit-period_start" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                            <input type="date" name="period_end" id="edit-period_end" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500" required>
                        </div>
                         <div class="col-span-2">
                            <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <select name="status" id="edit-status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="active">Active</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div id="edit-modal-footer" class="p-6 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex justify-end gap-3">
                <button type="button" data-modal-close class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancel</button>
                <button type="submit" form="editBudgetForm" class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">Save Changes</button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
             window.addEventListener('load', () => {
                // Charts
                const pieData = @json($charts['pie']);
                const barData = @json($charts['bar']);

                const catCtx = document.getElementById('categoryDoughnutChart')?.getContext('2d');
    
    // Pass the PHP data to JS safely
    const categoryData = @json($categoryChart ?? []);

    if (catCtx && Object.keys(categoryData).length > 0) {
        new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(categoryData),
                datasets: [{
                    data: Object.values(categoryData),
                    // Distinct colors for categories
                    backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#6366F1'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { usePointStyle: true } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) { label += ': '; }
                                label += '$' + context.raw.toLocaleString();
                                return label;
                            }
                        }
                    }
                },
                cutout: '65%' // Thinner ring for modern look
            }
        });
    } else if (catCtx) {
        // Optional: Handle empty state if no expenses yet
        catCtx.font = "14px Inter";
        catCtx.fillStyle = "#9CA3AF";
        catCtx.textAlign = "center";
        catCtx.fillText("No expense data available", catCtx.canvas.width/2, catCtx.canvas.height/2);
    }
                
                const pieCtx = document.getElementById('departmentPieChart')?.getContext('2d');
                if (pieCtx) {
                    new Chart(pieCtx, {
                        type: 'doughnut',
                        data: {
                            labels: Object.keys(pieData),
                            datasets: [{
                                data: Object.values(pieData),
                                backgroundColor: ['#6366F1', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
                                borderWidth: 0
                            }]
                        },
                        options: { responsive: true, plugins: { legend: { position: 'right', labels: { usePointStyle: true } } }, cutout: '70%' }
                    });
                }

                const barCtx = document.getElementById('departmentBarChart')?.getContext('2d');
                if (barCtx) {
                    new Chart(barCtx, {
                        type: 'bar',
                        data: {
                            labels: Object.keys(barData),
                            datasets: [
                                { label: 'Total', data: Object.values(barData).map(d => d.total), backgroundColor: '#E0E7FF', borderRadius: 4 },
                                { label: 'Spent', data: Object.values(barData).map(d => d.used), backgroundColor: '#6366F1', borderRadius: 4 }
                            ]
                        },
                        options: { responsive: true, scales: { y: { beginAtZero: true, grid: { display: false } }, x: { grid: { display: false } } }, plugins: { legend: { display: false } } }
                    });
                }

                // Bulk Actions
                const selectAll = document.getElementById('checkbox-all');
                const checkboxes = document.querySelectorAll('.budget-checkbox');
                if(selectAll) {
                    selectAll.addEventListener('change', e => {
                        checkboxes.forEach(c => c.checked = e.target.checked);
                    });
                }

                // Dropdown Toggle
                const btn = document.getElementById('dropdownActionButton');
                const menu = document.getElementById('dropdownAction');
                if(btn && menu) {
                    btn.addEventListener('click', () => menu.classList.toggle('hidden'));
                    document.addEventListener('click', e => {
                        if(!btn.contains(e.target) && !menu.contains(e.target)) menu.classList.add('hidden');
                    });
                }

                // View Modal
                const viewModal = document.getElementById('viewBudgetModal');
                const viewContent = document.getElementById('modal-body-content');
                document.querySelectorAll('.view-budget-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        viewModal.classList.remove('hidden');
                        const id = btn.dataset.budgetId;
                        viewContent.innerHTML = '<div class="text-center py-4">Loading...</div>';
                        fetch(`/budgets/${id}/json`)
                            .then(r => r.json())
                            .then(data => {
                                const used = parseFloat(data.used_amount || 0);
                                const total = parseFloat(data.total_amount || 0);
                                const pct = total > 0 ? (used / total * 100).toFixed(1) : 0;
                                const remaining = Math.max(total - used, 0);
                                const barColor = pct < 70 ? 'bg-green-500' : (pct < 90 ? 'bg-yellow-400' : 'bg-red-600');
                                viewContent.innerHTML = `
                                    <div class="space-y-4">
                                        <div class="flex justify-between border-b pb-2">
                                            <span class="text-gray-500">Name</span>
                                            <span class="font-medium text-gray-900 dark:text-white">${data.name}</span>
                                        </div>
                                        <div class="flex justify-between border-b pb-2">
                                            <span class="text-gray-500">Department</span>
                                            <span class="font-medium text-gray-900 dark:text-white">${data.department.name}</span>
                                        </div>
                                        <div class="flex justify-between border-b pb-2">
                                            <span class="text-gray-500">Total</span>
                                            <span class="font-medium text-gray-900 dark:text-white">$${total.toLocaleString()}</span>
                                        </div>
                                        <div class="space-y-2">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm font-semibold ${pct >= 90 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-300'}">
                                                    ${Math.min(pct, 100)}% Used
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    $${used.toLocaleString(undefined, {maximumFractionDigits: 0})} of $${total.toLocaleString(undefined, {maximumFractionDigits: 0})}
                                                </span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 overflow-hidden">
                                                <div class="h-full rounded-full transition-all duration-500 ${barColor}" 
                                                     style="width: ${Math.min(pct, 100)}%;">
                                                    
                                                </div>
                                            </div>
                                            <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                                <span>$${0}</span>
                                                <span>$${total.toLocaleString(undefined, {maximumFractionDigits: 0})}</span>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                    });
                });

                // Edit Modal
                const editModal = document.getElementById('editBudgetModal');
                const editForm = document.getElementById('editBudgetForm');
                const editLoading = document.getElementById('edit-modal-loading');
                const editFooter = document.getElementById('edit-modal-footer');

                document.querySelectorAll('.edit-budget-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        editModal.classList.remove('hidden');
                        editForm.classList.add('hidden');
                        editFooter.classList.add('hidden');
                        editLoading.classList.remove('hidden');
                        
                        const id = btn.dataset.budgetId;
                        editForm.action = `/budgets/${id}`;
                        
                        fetch(`/budgets/${id}/json`).then(r => r.json()).then(data => {
                            document.getElementById('edit-name').value = data.name;
                            document.getElementById('edit-department_id').value = data.department_id;
                            document.getElementById('edit-total_amount').value = data.total_amount;
                            document.getElementById('edit-period_start').value = data.period_start.split('T')[0];
                            document.getElementById('edit-period_end').value = data.period_end.split('T')[0];
                            document.getElementById('edit-status').value = data.status;
                            
                            editLoading.classList.add('hidden');
                            editForm.classList.remove('hidden');
                            editFooter.classList.remove('hidden');
                        });
                    });
                });

                // Close Modals
                document.querySelectorAll('[data-modal-close]').forEach(b => {
                    b.addEventListener('click', () => {
                        viewModal.classList.add('hidden');
                        editModal.classList.add('hidden');
                    });
                });
             });
        </script>
    @endpush
</x-app-layout>