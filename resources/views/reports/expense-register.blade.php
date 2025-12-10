@extends('livewire.layout.pdf')

@section('title', 'Expense Register')

@section('content')
    <div class="report-title">Detailed Expense Register</div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 80px;">Date</th>
                <th>Reference / Description</th>
                <th>Category</th>
                <th>User / Dept</th>
                <th>Status</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $expense)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($expense->transaction_date)->format('d/m/Y') }}</td>
                    <td>
                        <strong>#{{ $expense->id }}</strong> - {{ Str::limit($expense->description, 50) }}
                        <br>
                        <span class="meta-data">Budget: {{ $expense->budget->name ?? 'N/A' }}</span>
                    </td>
                    <td>{{ $expense->category }}</td>
                    <td>
                        {{ $expense->user->name ?? 'Unknown' }}<br>
                        <span class="meta-data">{{ $expense->department->name ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <span class="label {{ $expense->status === 'approved' ? 'label-success' : 'label-warning' }}">
                            {{ ucfirst($expense->status) }}
                        </span>
                    </td>
                    <td class="text-right font-bold">
                        RM {{ number_format($expense->amount, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px;">No expenses found for this period.</td>
                </tr>
            @endforelse
            
            <tr style="background-color: #f3f4f6; border-top: 2px solid #4f46e5;">
                <td colspan="5" class="text-right font-bold">TOTAL EXPENSES</td>
                <td class="text-right font-bold">RM {{ number_format($expenses->sum('amount'), 2) }}</td>
            </tr>
        </tbody>
    </table>
@endsection