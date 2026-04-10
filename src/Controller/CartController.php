<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Cart;
use App\Entity\CartItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
// pour récupérer les données du formulaire
use Symfony\Component\HttpFoundation\Request;
// permet d'utiliser la session(pour le panier)
use Symfony\Component\HttpFoundation\Session\SessionInterface;







// pour récupérer les produits de la base de données
use App\Repository\SweatshirtRepository;

final class CartController extends AbstractController
{    
     private $em;
    

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    private function getUserCart(): ?Cart
    {
        $user = $this->getUser();

        if (!$user instanceof User) 
            {
                return null;
            }; // si pas connecté

        $cart = $this->em->getRepository(Cart::class)->findOneBy(['user' => $user]);

        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $cart->setCreatedAt(new \DateTimeImmutable());
            $cart->setUpdatedAt(new \DateTimeImmutable());
            $this->em->persist($cart);
            $this->em->flush();
        }

        return $cart;
    }
    
//route pour afficher le panier
    #[Route('/cart', name: 'cart')]
public function index(): Response
{
    $cart = $this->getUserCart();
    // Ajoute ça juste après pour vérifier le contenu du panier
    //dd($cart->getCartItems()->toArray());



    $cartItems = $cart ? $cart->getCartItems()->toArray() : [];

    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item->getProduct()->getPrice() * $item->getQuantity();
    }

    return $this->render('cart/index.html.twig', [
        'cart' => $cartItems,
        'total' => $total,
        'paymentSuccess' =>  false // panier normal
    ]);
}
   
   #[Route('/cart/add/{id}', name: 'cart_add')]
public function add($id, Request $request, SweatshirtRepository $repo): Response
{
    $user = $this->getUser();
    if (!$user instanceof User) {
        $this->addFlash('error', 'Vous devez être connecté pour ajouter un produit.');
        return $this->redirectToRoute('app_login');
    }

    $cart = $this->getUserCart(); // panier persistant
    $product = $repo->find($id);
    if (!$product) {
        throw $this->createNotFoundException('Produit non trouvé');
    }

    $size = $request->request->get('size')?? $request->query->get('size');// Récupérer la taille depuis le formulaire ou la query string(post ou get)

    if (!$size) {
        $this->addFlash('error', 'Veuillez sélectionner une taille.');
        return $this->redirectToRoute('products', ['id' => $id]);
    }

    // Vérifier si CartItem existe déjà pour ce produit et cette taille
    $existingItem = $this->em->getRepository(CartItem::class)->findOneBy([
        'cart' => $cart,
        'product' => $product,
        'size' => $size
    ]);

    if ($existingItem) {
        $existingItem->setQuantity($existingItem->getQuantity() + 1);
    } else {
        $cartItem = new CartItem();
        $cartItem->setCart($cart)
                 ->setProduct($product)
                 ->setSize($size)
                 ->setQuantity(1);
        $this->em->persist($cartItem);
    }

    $cart->setUpdatedAt(new \DateTimeImmutable());
    $this->em->flush();

    return $this->redirectToRoute('cart');
}


   #[Route('/cart/increase/{id}/{size}', name: 'cart_increase')]
public function increase($id, $size, SweatshirtRepository $repo): Response
{
    $cart = $this->getUserCart(); // panier persistant
    if (!$cart) return $this->redirectToRoute('app_login');// si pas connecté

    $item = $this->em->getRepository(CartItem::class)->findOneBy([ // on cherche l'item correspondant au produit et à la taille dans le panier de l'utilisateur
        'cart' => $cart,
        'product' => $repo->find($id),
        'size' => $size
    ]);

    if ($item) { // si l'item existe déjà, on augmente la quantité de 1
        $item->setQuantity($item->getQuantity() + 1);
        $cart->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();
    }

    return $this->redirectToRoute('cart');
}

   #[Route('/cart/decrease/{id}/{size}', name: 'cart_decrease')]
public function decrease($id, $size, SweatshirtRepository $repo): Response
{
    $cart = $this->getUserCart();
    if (!$cart) return $this->redirectToRoute('app_login');

    $item = $this->em->getRepository(CartItem::class)->findOneBy([
        'cart' => $cart,
        'product' => $repo->find($id),
        'size' => $size
    ]);

    if ($item) {
        if ($item->getQuantity() > 1) {
            $item->setQuantity($item->getQuantity() - 1);
        } else {
            $this->em->remove($item);
        }
        $cart->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();
    }

    return $this->redirectToRoute('cart');
}

            

            
#[Route('/cart/remove/{id}/{size}', name: 'cart_remove')]
public function remove($id, $size, SweatshirtRepository $repo): Response
{
    $cart = $this->getUserCart();
    if (!$cart) return $this->redirectToRoute('app_login');

    $item = $this->em->getRepository(CartItem::class)->findOneBy([
        'cart' => $cart,
        'product' => $repo->find($id),
        'size' => $size
    ]);

    if ($item) {
        $this->em->remove($item);
        $cart->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();
    }

    return $this->redirectToRoute('cart');
}
    

    #[Route('/success', name: 'checkout_success')]
public function success(): Response
{
    $cart = $this->getUserCart();
    if (!$cart) return $this->redirectToRoute('app_login');

    $cartItems = $cart->getCartItems()->toArray();
    $total = 0;

    foreach ($cartItems as $item) {
        $total += $item->getProduct()->getPrice() * $item->getQuantity();
    }

    // Après la commande réussie, on supprime tous les items du panier
    foreach ($cartItems as $item) {
        $this->em->remove($item);
    }

    $cart->setUpdatedAt(new \DateTimeImmutable());
    $this->em->flush();

    return $this->render('payment/success.html.twig', [
        'cart' => $cartItems,
        'total' => $total,
        'paymentSuccess' =>  true // panier après paiement réussi
    ]);
}

}
