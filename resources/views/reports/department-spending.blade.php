@extends('livewire.layout.pdf')

@section('title', 'Department Analysis')

@section('content')
    <div class="report-title">Department Spending Analysis</div>
    
    <div style="margin-bottom: 30px;">
        @foreach($departments as $dept)
            <div style="margin-bottom: 20px; page-break-inside: avoid;">
                <h3 style="background-color: #eee; padding: 5px 10px; margin-bottom: 0;">{{ $dept->name }}</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Metric</th>
                            <th class="text-right">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Total Budgets Allocation</td>
                            <td class="text-right">RM {{ number_format($dept->budgets->sum('total_amount'), 2) }}</td>
                        </tr>
                        <tr>
                            <td>Total Actual Spending</td>
                            <td class="text-right">RM {{ number_format($dept->expenses->where('status', 'approved')->sum('amount'), 2) }}</td>
                        </tr>
                        <tr>
                            <td>Budget Utilization</td>
                            @php
                                $total = $dept->budgets->sum('total_amount');
                                $used = $dept->expenses->where('status', 'approved')->sum('amount');
                                $utilization = $total > 0 ? ($used / $total) * 100 : 0;
                            @endphp
                            <td class="text-right">{{ number_format($utilization, 1) }}%</td>
                        </tr>
                        <tr>
                            <td>Highest Spending Category</td>
                            <td class="text-right">
                                {{ 
                                    $dept->expenses
                                        ->where('status', 'approved')
                                        ->groupBy('category')
                                        ->map(fn($row) => $row->sum('amount'))
                                        ->sortDesc()
                                        ->keys()
                                        ->first() ?? 'N/A' 
                                }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
@endsection