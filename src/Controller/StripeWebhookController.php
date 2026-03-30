<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StripeWebhookController extends AbstractController
{
    #[Route('/stripe/webhook', name: 'stripe_webhook', methods: ['POST'])]
    public function index(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('Stripe-Signature');
        $secret = $_ENV['STRIPE_WEBHOOK_SECRET'];

        try {
            \Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $secret
            );
        } catch (\Exception $e) {
            return new Response('Invalid signature', 400);
        }

        // 👉 Ici tu traites les événements
        if ($event->type === 'checkout.session.completed') {
            // Exemple
            // $session = $event->data->object;
            // Sauvegarder la commande, envoyer un email, etc.
        }

        return new Response('OK', 200);
    }
}