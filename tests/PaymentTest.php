<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PaymentTest extends WebTestCase
{
    public function testStripeCheckout()
    {
        $client = static::createClient();

        // Page de test 
        $client->request('GET', '/checkout');

        // Vérifie que la page répond (si user connecté)
        $this->assertResponseStatusCodeSame(302); // redirection vers login si pas connecté
    }
}