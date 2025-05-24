<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Invalid Credentials'], 401);
        }

        return response()->json(compact('token'));
    }

    public function logout(): JsonResponse
    {
        auth('api')->invalidate(auth('api')->getToken());

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function me(): JsonResponse
    {
        $user = auth('api')->user();
        $availableCount = Payment::where('user_id', $user->id)
            ->where('status', 'paid')
            ->sum('remaining_requests');

        $user->remaining_requests = intval($availableCount);
        return response()->json($user);

    }
}
