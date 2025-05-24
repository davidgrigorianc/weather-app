<?php
namespace App\Services;

use App\Mail\UserCreated;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserNotificationService
{
    public function sendUserCreatedEmail(User $user, string $password): bool
    {
        try {
            Mail::to($user->email)->queue(new UserCreated($user, $password));
            return true;
        } catch (\Throwable $e) {
            Log::error('sendUserCreatedEmail mail failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage()
            ]);
            return false;
        }
    }
}
