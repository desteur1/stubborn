<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Sweatshirt;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CartTest extends WebTestCase
{
    private $client;
    private EntityManagerInterface $em;
    private Sweatshirt $product;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->em = self::getContainer()
            ->get(EntityManagerInterface::class);

        $this->product = $this->em
            ->getRepository(Sweatshirt::class)
            ->findOneBy(['name' => 'Test Sweatshirt']);

        $this->assertNotNull($this->product);
    }

    private function login(): void
    {
        $user = $this->em
            ->getRepository(User::class)
            ->findOneBy(['email' => 'test@test.com']);

        $this->assertNotNull($user);

        $this->client->loginUser($user);
    }

    public function testAddToCart(): void
    {
        $this->login();

        $this->client->request(
            'GET',
            '/cart/add/' . $this->product->getId() . '?size=M'
        );

        $this->assertResponseRedirects('/cart');
    }

    public function testIncreaseQuantity(): void
    {
        $this->login();

        $this->client->request(
            'GET',
            '/cart/add/' . $this->product->getId() . '?size=M'
        );

        $this->client->request(
            'GET',
            '/cart/increase/' . $this->product->getId() . '/M'
        );

        $this->assertResponseRedirects('/cart');
    }

    public function testDecreaseQuantity(): void
    {
        $this->login();

        $this->client->request(
            'GET',
            '/cart/add/' . $this->product->getId() . '?size=M'
        );

        $this->client->request(
            'GET',
            '/cart/decrease/' . $this->product->getId() . '/M'
        );

        $this->assertResponseRedirects('/cart');
    }

    public function testRemoveItem(): void
    {
        $this->login();

        $this->client->request(
            'GET',
            '/cart/add/' . $this->product->getId() . '?size=M'
        );

        $this->client->request(
            'GET',
            '/cart/remove/' . $this->product->getId() . '/M'
        );

        $this->assertResponseRedirects('/cart');
    }

    public function testInvalidProduct(): void
    {
        $this->login();

        $this->client->request(
            'GET',
            '/cart/add/999999?size=M'
        );

        $this->assertContains(
            $this->client->getResponse()->getStatusCode(),
            [404, 302]
        );
    }

    public function testNotLoggedUserRedirect(): void
    {
        $this->client->request(
            'GET',
            '/cart/add/' . $this->product->getId() . '?size=M'
        );

        $this->assertResponseRedirects('/login');
    }
}