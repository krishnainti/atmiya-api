<?php

namespace App\Src\Payment;

use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class Paypal
{

    public function __construct()
    {

    }

    /**
     * process transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function initiatePayment($amount, $currency = 'USD')
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => config('paypal.return_url'),
                "cancel_url" => config('paypal.cancel_url'),
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => $currency,
                        "value" => $amount,
                    ],
                ],
            ],
        ]);

        Log::debug('-------------------processTransaction----------------');
        Log::debug($response);
        Log::debug('-------------------processTransaction end----------------');

        if (isset($response['id']) && $response['id'] != null) {
            // redirect to approve href
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return [
                        'status' => true,
                        'id' => $response['id'],
                        'redirect_url' => $links['href'],
                    ];
                }
            }

            return [
                'status' => false,
                'id' => $response['id'],
                'redirect_url' => null,
            ];

        } else {
            return [
                'status' => false,
                'id' => null,
                'redirect_url' => null,
            ];
        }
    }

    public function capturePaymentOrder($id)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($id);

        Log::debug('--------------------successTransaction-----------------');
        Log::debug($response);
        Log::debug('--------------------successTransaction end-----------------');

       return  $response;
    }

}
