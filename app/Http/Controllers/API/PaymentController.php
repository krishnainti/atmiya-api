<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Payment;
use App\Src\Payment\Paypal;
use Illuminate\Http\Request;

class PaymentController extends BaseController
{
    public function capturePaymentOrder(Request $request)
    {
        $payment = Payment::where('payment_id', $request->id)->first();

        if (empty($payment)) {
            return $this->sendError('Profile.', ['error' => 'Unauthorized']);
        }

        $paypal = new Paypal();
        return $paypal->capturePaymentOrder($payment->payment_id);

    }
}
