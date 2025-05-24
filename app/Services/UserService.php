<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordGeneratorService $passwordGenerator,
        private UserNotificationService $notificationService,
    ) {}

    public function getAllUsers(): Collection
    {
        return $this->userRepository->all();
    }

    public function createUser(array $data): array
    {
        $password = $this->passwordGenerator->generate();

        $user = $this->userRepository->create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($password),
        ]);

        $emailSent = $this->notificationService->sendUserCreatedEmail($user, $password);

        return ['user' => $user, 'emailSent' => $emailSent];
    }

    public function resendUserEmail(User $user): bool
    {
        $password = $this->passwordGenerator->generate();
        $user->update(['password' => Hash::make($password)]);

        return $this->notificationService->sendUserCreatedEmail($user, $password);
    }
}
