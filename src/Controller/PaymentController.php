<?php

namespace App\Controller;

use App\Repository\CartRepository;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PaymentController extends AbstractController
{
    private CartRepository $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

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

        // Calcul du montant total
        $amount = 0;
        foreach ($cart->getCartItems() as $item) {
            $amount += $item->getProduct()->getPrice() * $item->getQuantity() * 100;
        }

        // Création du PaymentIntent côté serveur
        Stripe::setApiKey($this->getParameter('stripe_secret_key'));

        $paymentIntent = PaymentIntent::create([
            'amount'   => $amount,
            'currency' => 'eur',
        ]);
        // Convertir le montant en euros pour l'affichage
        $total = $amount / 100;

        return $this->render('payment/checkout.html.twig', [
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
            'client_secret'     => $paymentIntent->client_secret,
            'total'             => $total,
            'cartWithData'       => $cart->getCartItems(),// Passer les items du panier à la vue
        ]);
    }
}