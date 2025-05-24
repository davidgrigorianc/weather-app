<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\WeatherSearchRequest;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;

class WeatherController extends Controller
{
    public function __construct(protected WeatherService $weather){}

    public function search(WeatherSearchRequest $request): JsonResponse
    {
        $user = auth('api')->user();
        $city = $request->input('city');

        $result = $this->weather->searchWeather($city, $user->id);

        return response()->json($result['response'], $result['status']);
    }
}
