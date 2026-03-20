<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// permet d'utiliser la session(pour le panier)
use Symfony\Component\HttpFoundation\Session\SessionInterface;

// pour récupérer les produits de la base de données
use App\Repository\SweatshirtRepository;

final class CartController extends AbstractController
{    //route pour afficher le panier
    #[Route('/cart', name: 'cart')]
    public function index(SessionInterface $session, SweatshirtRepository $repo): Response
    {
         $cart = $session->get('cart', []); // récupère le panier de la session, ou un tableau vide si il n'existe pas
            $cartWithData = []; // tableau pour stocker les produits du panier avec leurs données
            
            $total = 0; // variable pour stocker le total du panier

            foreach ($cart as $id => $quantity) { // on parcourt chaque produit du panier
                $product = $repo->find($id); // récupère le produit correspondant à l'id

                if ($product) { // si le produit existe
                    $cartWithData[] = [ // on ajoute le produit et sa quantité au tableau
                        'product' => $product,
                        'quantity' => $quantity
                    ];
                    $total += $product->getPrice() * $quantity; // on ajoute le prix du produit * la quantité au total
                }
            }


        return $this->render('cart/index.html.twig', [
            'cart' => $cartWithData, // on passe le tableau des produits du panier à la vue
            'total' => $total // on passe le total du panier à la vue
        ]);
    }


    //route pour ajouter un produit au panier
    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add($id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []); // récupère le panier de la session, ou un tableau vide si il n'existe pas

        if (!empty($cart[$id])) { // si le produit est déjà dans le panier
            $cart[$id]++; // on augmente la quantité de 1
        } else {
            $cart[$id] = 1; // sinon on ajoute le produit au panier avec une quantité de 1
        }

        $session->set('cart', $cart); // on enregistre le panier dans la session
     
         return $this->redirectToRoute('cart'); // on redirige vers la page du panier
    }


    #[Route('/cart/increase/{id}', name: 'cart_increase')]
    public function increase($id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []); // récupère le panier de la session, ou un tableau vide si il n'existe pas

        if (!empty($cart[$id])) { // si le produit est dans le panier 
            $cart[$id]++; // on augmente la quantité de 1
        }

        $session->set('cart', $cart); // on enregistre le panier dans la session
     
         return $this->redirectToRoute('cart'); // on redirige vers la page du panier
    }


    #[Route('/cart/decrease/{id}', name: 'cart_decrease')]
    public function decrease($id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []); // récupère le panier de
        if (!empty($cart[$id])) { // si le produit est dans le panier
            if ($cart[$id] > 1) { // si la quantité est supérieure à 1
                $cart[$id]--; // on diminue la quantité de 1
            } else {
                unset($cart[$id]); // sinon on supprime le produit du panier
            }
        }
        $session->set('cart', $cart); // on enregistre le panier dans la session

            return $this->redirectToRoute('cart'); // on redirige vers la page du panier
    }

            

            

    //route pour supprimer un produit du panier
    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove($id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []); // récupère le panier de
        if (!empty($cart[$id])) { // si le produit est dans le panier
            unset($cart[$id]); // on le supprime du panier
        }
        $session->set('cart', $cart); // on enregistre le panier dans la session
     
         return $this->redirectToRoute('cart'); // on redirige vers la page du panier

    }



}
