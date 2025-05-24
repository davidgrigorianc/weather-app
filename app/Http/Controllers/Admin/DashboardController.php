<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected UserService $userService)
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $users = $this->userService->getAllUsers();
        return view('admin.dashboard', compact('users'));
    }

    public function storeUser(StoreUserRequest $request): RedirectResponse
    {
        try {
            $userCreated = $this->userService->createUser($request->validated());

            if ($userCreated) {
                return redirect()->back()->with('status', 'User Has Been Created Successfully');
            } else {
                return redirect()->back()->withErrors('Failed to Create User');
            }
        } catch (\Throwable $e) {
            logger()->error('Resend failed', ['request' => $request->validated(), 'error' => $e->getMessage()]);

            return redirect()->back()->withErrors('Failed to resend email: ' . $e->getMessage());
        }

    }

    public function resendEmail(User $user): RedirectResponse
    {
        try {
            $emailSent = $this->userService->resendUserEmail($user);

            if ($emailSent) {
                return redirect()->back()->with('status', 'Email resent successfully.');
            } else {
                return redirect()->back()->withErrors('Failed to resend email.');
            }
        } catch (\Throwable $e) {
            logger()->error('Resend failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);

            return redirect()->back()->withErrors('Failed to resend email: ' . $e->getMessage());
        }
    }
}
