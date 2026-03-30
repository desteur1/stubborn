<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use App\Repository\SweatshirtRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends AbstractController
{
    #[Route('/create-checkout-session', name: 'create_checkout_session', methods:['POST'])]
    public function create(SessionInterface $session, SweatshirtRepository $repo): JsonResponse
    {
        $cart = $session->get('cart', []);

        if (empty($cart)) {
            return new JsonResponse(['error' => 'Panier vide'], 400);
        }

        Stripe::setApiKey($this->getParameter('stripe_secret_key'));

        $lineItems = [];

        foreach ($cart as $id => $sizes) {
            $product = $repo->find($id);
            if (!$product) continue;

            foreach ($sizes as $size => $quantity) {

                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $product->getName() . " ($size)"
                        ],
                        'unit_amount' => $product->getPrice() * 100,
                    ],
                    'quantity' => $quantity,
                ];
            }
        }

        $sessionStripe = StripeSession::create([
            'mode' => 'payment',
            'ui_mode'=>'embedded',
            'line_items' => $lineItems,
            'return_url' => $this->generateUrl('checkout_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            
        ]);

        return new JsonResponse([
            'clientSecret' => $sessionStripe->client_secret]);
    }
    #[Route('/create-payment-intent', name: 'create_payment_intent', methods:['POST'])]
public function createPaymentIntent(SessionInterface $session, SweatshirtRepository $repo): JsonResponse
{
    $cart = $session->get('cart', []);
    if (empty($cart)) {
        return new JsonResponse(['error' => 'Panier vide'], 400);
    }

    Stripe::setApiKey($this->getParameter('stripe_secret_key'));

    $amount = 0;
    foreach ($cart as $id => $sizes) {
        $product = $repo->find($id);
        if (!$product) continue;
        foreach ($sizes as $size => $quantity) {
            $amount += $product->getPrice() * 100 * $quantity; // montant en centimes
        }
    }

    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $amount,
        'currency' => 'eur',
    ]);

    return new JsonResponse([
        'clientSecret' => $paymentIntent->client_secret
    ]);
}

    #[Route('/checkout', name: 'checkout')]
    public function checkout(): Response
    {
        return $this->render('payment/checkout.html.twig' ,[
            'stripe_public_key' => $this->getParameter('stripe_public_key')
        ]);
    }
    
}