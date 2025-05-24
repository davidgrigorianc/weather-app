<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTariffCheckoutSessionRequest;
use App\Models\Tariff;
use App\Services\StripePaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(protected StripePaymentService $stripeService) {}

    public function createCheckoutSession(CreateTariffCheckoutSessionRequest $request): JsonResponse
    {
        $tariff = Tariff::findOrFail($request->tariff_id);
        $result = $this->stripeService->createCheckoutSession($tariff, $request->base_url);

        return response()->json($result);
    }

    public function handleWebhook(Request $request): JsonResponse
    {
        Log::debug('Stripe Webhook Received', [
            'headers' => $request->headers->all(),
            'payload' => $request->all(),
        ]);

        $sig = $request->header('Stripe-Signature');
        $payload = $request->getContent();

        if (!$sig || !$payload) {
            Log::error('Webhook missing signature or payload');
            return response()->json(['error' => 'Invalid webhook'], 400);
        }

        try {
            $this->stripeService->handleWebhook($payload, $sig);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook error'], 400);
        }

        return response()->json(['status' => 'success']);
    }
}
