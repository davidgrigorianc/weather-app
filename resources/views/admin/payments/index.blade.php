@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Payments</h1>

        <div class="table-responsive shadow-sm bg-white p-3 rounded">
            <table class="table table-hover align-middle">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Status</th>
                    <th>Initial Requests</th>
                    <th>Remaining Requests</th>
                    <th>Amount</th>
                    <th>Created At</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>{{ $payment->user->name ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-{{ $payment->status === 'paid' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td>{{ $payment->initial_requests }}</td>
                        <td>{{ $payment->remaining_requests }}</td>
                        <td>${{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No payments found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $payments->links('pagination::bootstrap-4')}}
            </div>
        </div>
    </div>
@endsection
