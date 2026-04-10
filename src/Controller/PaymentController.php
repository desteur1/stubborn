<?php

namespace App\Controller;

use App\Repository\CartRepository;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\StripeService; // import du service 

class PaymentController extends AbstractController
{
    public function __construct(
        private CartRepository $cartRepository,
        private StripeService $stripeService // 👈 injection du service
    ) {}

    #[Route('/checkout', name: 'checkout')]
    public function checkout(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException("Vous devez être connecté");
        }

        $cart = $this->cartRepository->findOneBy(['user' => $user]);

        if (!$cart || count($cart->getCartItems()) === 0) {
            throw $this->createNotFoundException("Panier vide");
        }

        // Calcul total
        $amount = 0;
        foreach ($cart->getCartItems() as $item) {
            $amount += $item->getProduct()->getPrice() * $item->getQuantity() * 100;
        }

        // 👇 UTILISATION DU SERVICE
        $paymentIntent = $this->stripeService->createPaymentIntent($amount);

        return $this->render('payment/checkout.html.twig', [
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
            'client_secret' => $paymentIntent->client_secret,
            'total' => $amount / 100,
            'cartWithData' => $cart->getCartItems(),
        ]);
    }
}