<?php
namespace App\DTO;

class PaymentDTO
{
    public function __construct(
        public int $tariffId,
        public int $userId,
        public float $amount,
        public string $status,
        public string $paymentSystem,
        public string $transactionId,
        public int $initialRequests,
        public int $remainingRequests,
    ) {}
}
