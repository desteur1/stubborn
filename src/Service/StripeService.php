<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeService
{
    private string $secretKey;

    public function __construct(string $stripeSecretKey)
    {
        $this->secretKey = $stripeSecretKey;
    }

    public function createPaymentIntent(int $amount): PaymentIntent
    {
        Stripe::setApiKey($this->secretKey);

        return PaymentIntent::create([
            'amount' => $amount,
            'currency' => 'eur',
        ]);
    }
}