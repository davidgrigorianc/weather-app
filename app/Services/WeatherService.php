<?php
namespace App\Services;

use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Support\Facades\Http;

class WeatherService
{
    public function __construct(
        protected PaymentRepositoryInterface $payments
    ){}

    public function searchWeather(string $city, int $userId): array
    {
        $remaining = $this->payments->getTotalRemainingRequests($userId);

        if ($remaining <= 0) {
            return [
                'success' => false,
                'status' => 401,
                'response' => [
                    'error' => 'Failed to search',
                    'message' => 'You don\'t have enough remaining requests.'
                ]
            ];
        }

        $apiKey = config('weather.key');
        $url = "https://api.openweathermap.org/data/2.5/weather";

        try {
            $response = Http::get($url, [
                'q' => $city,
                'appid' => $apiKey,
                'units' => 'metric',
            ]);

            if ($response->successful()) {
                $this->payments->decrementFirstEligibleRequest($userId);
                return [
                    'success' => true,
                    'status' => 200,
                    'response' => $response->json()
                ];
            }

            return [
                'success' => false,
                'status' => $response->status(),
                'response' => [
                    'error' => 'City not found or weather service error.',
                    'details' => $response->json()
                ]
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'status' => 500,
                'response' => [
                    'error' => 'Something went wrong while contacting weather service.',
                    'message' => $e->getMessage()
                ]
            ];
        }
    }
}
