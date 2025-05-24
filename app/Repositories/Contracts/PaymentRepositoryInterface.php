<?php
namespace App\Repositories\Contracts;

use App\DTO\PaymentDTO;
use App\Models\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PaymentRepositoryInterface
{
    public function create(PaymentDTO $paymentDTO): Payment;

    public function findByTransactionId(string $transactionId): ?Payment;

    public function getTotalRemainingRequests(int $userId): int;

    public function decrementFirstEligibleRequest(int $userId): void;

    public function paginateWithUser(int $perPage = 10): LengthAwarePaginator;
}
