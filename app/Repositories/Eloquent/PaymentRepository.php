<?php
namespace App\Repositories\Eloquent;

use App\DTO\PaymentDTO;
use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function create(PaymentDTO $paymentDTO): Payment
    {
        return Payment::create([
            'tariff_id' => $paymentDTO->tariffId,
            'user_id' => $paymentDTO->userId,
            'amount' => $paymentDTO->amount,
            'status' => $paymentDTO->status,
            'payment_system' => $paymentDTO->paymentSystem,
            'transaction_id' => $paymentDTO->transactionId,
            'initial_requests' => $paymentDTO->initialRequests,
            'remaining_requests' => $paymentDTO->remainingRequests,
        ]);
    }

    public function findByTransactionId(string $transactionId): ?Payment
    {
        return Payment::where('transaction_id', $transactionId)->first();
    }

    public function getTotalRemainingRequests(int $userId): int
    {
        return Payment::where('user_id', $userId)
            ->where('status', 'paid')
            ->sum('remaining_requests');
    }

    public function decrementFirstEligibleRequest(int $userId): void
    {
        $payment = Payment::where('user_id', $userId)
            ->where('status', 'paid')
            ->where('remaining_requests', '>', 0)
            ->orderBy('id')
            ->first();

        if ($payment) {
            $payment->decrement('remaining_requests');
        }
    }

    public function paginateWithUser(int $perPage = 10): LengthAwarePaginator
    {
        return Payment::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
