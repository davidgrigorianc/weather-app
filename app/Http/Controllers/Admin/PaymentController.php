<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Contracts\View\View;

class PaymentController extends Controller
{
    protected PaymentRepositoryInterface $paymentRepository;

    public function __construct(PaymentRepositoryInterface $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function index(Request $request): View
    {
        $payments = $this->paymentRepository->paginateWithUser(10);

        return view('admin.payments.index', compact('payments'));
    }
}
