@extends('livewire.layout.pdf')

@section('title', 'Budget Variance Report')

@section('content')
    <div class="report-title">Budget Variance Report</div>
    <p class="meta-data">Period: {{ $start_date ?? 'All Time' }} to {{ $end_date ?? 'Present' }}</p>

    <table class="summary-box">
        <tr>
            <td><strong>Total Budgeted:</strong> RM {{ number_format($budgets->sum('total_amount'), 2) }}</td>
            <td><strong>Total Used:</strong> RM {{ number_format($budgets->sum('used_amount'), 2) }}</td>
            <td><strong>Overall Variance:</strong> RM {{ number_format($budgets->sum('total_amount') - $budgets->sum('used_amount'), 2) }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Budget Name</th>
                <th>Department</th>
                <th class="text-right">Allocated</th>
                <th class="text-right">Used</th>
                <th class="text-right">Variance</th>
                <th class="text-center">% Used</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($budgets as $budget)
                @php
                    $variance = $budget->total_amount - $budget->used_amount;
                    $percent = $budget->total_amount > 0 ? ($budget->used_amount / $budget->total_amount) * 100 : 0;
                    $statusClass = $percent >= 100 ? 'label-danger' : ($percent >= 80 ? 'label-warning' : 'label-success');
                @endphp
                <tr>
                    <td>
                        {{ $budget->name }}<br>
                        <span class="meta-data">{{ $budget->project->name ?? 'No Project' }}</span>
                    </td>
                    <td>{{ $budget->department->name ?? 'N/A' }}</td>
                    <td class="text-right">RM {{ number_format($budget->total_amount, 2) }}</td>
                    <td class="text-right">RM {{ number_format($budget->used_amount, 2) }}</td>
                    <td class="text-right {{ $variance < 0 ? 'text-red' : 'text-green' }}">
                        RM {{ number_format($variance, 2) }}
                    </td>
                    <td class="text-center">{{ number_format($percent, 1) }}%</td>
                    <td class="text-center">
                        <span class="label {{ $statusClass }}">
                            {{ $percent >= 100 ? 'Over' : 'Active' }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection