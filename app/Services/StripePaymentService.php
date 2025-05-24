<?php

namespace App\Services;

use App\DTO\PaymentDTO;
use App\Models\Tariff;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripePaymentService
{
    const string STATUS_PENDING = 'pending';
    const string STATUS_PAID = 'paid';


    public function __construct(protected PaymentRepositoryInterface $paymentRepository)
    {
        Stripe::setApiKey(config('stripe.secret'));
    }

    /**
     * @param Tariff $tariff
     * @param string $baseUrl
     * @return array{url: string, payment_id: int}
     * @throws \Exception
     */
    public function createCheckoutSession(Tariff $tariff, string $baseUrl): array
    {
        $userId = Auth::id();
        if (!$userId) {
            throw new \Exception('User must be authenticated to create checkout session.');
        }

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => "Tariff Package: {$tariff->key}"],
                    'unit_amount' => $tariff->price * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => "$baseUrl/success?session_id={CHECKOUT_SESSION_ID}&tariff_key={$tariff->key}&amount={$tariff->price}",
            'cancel_url' => "$baseUrl/cancel",
            'metadata' => [
                'tariff_id' => $tariff->id,
                'tariff_key' => $tariff->key,
                'user_id' => $userId,
                'amount' => $tariff->price,
            ],
        ]);

        $paymentData = new PaymentDTO(
            tariffId: $tariff->id,
            userId: $userId,
            amount: $tariff->price,
            status: self::STATUS_PENDING,
            paymentSystem: 'stripe',
            transactionId: $session->id,
            initialRequests: $tariff->count,
            remainingRequests: $tariff->count,
        );

        $payment = $this->paymentRepository->create($paymentData);

        Log::info('Stripe checkout session created', [
            'session_id' => $session->id,
            'payment_id' => $payment->id,
            'user_id' => $userId,
        ]);

        return ['url' => $session->url, 'payment_id' => $payment->id];
    }

    /**
     * @param string $payload
     * @param string $sigHeader
     * @return void
     */
    public function handleWebhook(string $payload, string $sigHeader): void
    {
        $endpointSecret = config('stripe.webhook_key');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException | SignatureVerificationException $e) {
            Log::error('Stripe webhook verification failed: ' . $e->getMessage());
            return;
        }

        match ($event->type) {
            'checkout.session.completed' => $this->handleSessionCompleted($event->data->object),
            'payment_intent.succeeded' => $this->handlePaymentIntentSucceeded($event->data->object),
            default => Log::info("Unhandled Stripe event type: {$event->type}"),
        };
    }

    private function handleSessionCompleted(object $session): void
    {
        Log::info("Processing checkout.session.completed", ['session_id' => $session->id]);

        $payment = $this->paymentRepository->findByTransactionId($session->id);
        if ($payment) {
            $this->paymentRepository->updateStatus($payment->id, self::STATUS_PAID);
            Log::info("Payment {$payment->id} marked as paid (checkout.session.completed).");
        } else {
            Log::warning("No payment found for session: {$session->id}");
        }
    }

    private function handlePaymentIntentSucceeded(object $intent): void
    {
        Log::info("Processing payment_intent.succeeded", ['intent_id' => $intent->id]);

        $payment = $this->paymentRepository->findByTransactionId($intent->id);
        if ($payment) {
            $this->paymentRepository->updateStatus($payment->id, self::STATUS_PAID);
            Log::info("Payment {$payment->id} marked as paid (payment_intent.succeeded).");
        } else {
            Log::warning("No payment found for intent: {$intent->id}");
        }
    }
}
