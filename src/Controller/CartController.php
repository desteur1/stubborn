<?php

namespace App\Controller;

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
{    //route pour afficher le panier
    #[Route('/cart', name: 'cart')]
    public function index(SessionInterface $session, SweatshirtRepository $repo): Response
    {
    
         $cart = $session->get('cart', []); // récupère le panier de la session, ou un tableau vide si il n'existe pas
            $cartWithData = []; // tableau pour stocker les produits du panier avec leurs données
            
            $total = 0; // variable pour stocker le total du panier



            foreach ($cart as $id => $sizes) { // pour chaque produit dans le panier, on récupère son id et les tailles associées
                $product = $repo->find($id); // récupère le produit correspondant à l'id
                if (!$product) continue; // si le produit n'existe pas, on passe au suivant

                foreach ($sizes as $size => $quantity) { // pour chaque taille du produit, on récupère la taille et la quantité

                if ($product) { // si le produit existe
                    $cartWithData[] = [ // on ajoute le produit et sa quantité au tableau
                        'product' => $product,
                        'size' => $size,
                        'quantity' => $quantity
                    ];
                    $total += $product->getPrice() * $quantity; // on ajoute le prix du produit * la quantité au total
                }
            }
        }
        // dd($cartWithData);
        //$session->clear();
        return $this->render('cart/index.html.twig', [
            'cart' => $cartWithData, // on passe le tableau des produits du panier à la vue
            'total' => $total // on passe le total du panier à la vue
        ]);
    }

   
    //route pour ajouter un produit au panier
    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add($id, Request $request, SessionInterface $session): Response
    {   
        $size = $request->request->get('size'); // récupère la taille sélectionnée par l'utilisateur

        $cart = $session->get('cart', []); // récupère le panier de la session, ou un tableau vide si il n'existe pas
        if (!isset($cart[$id])) { // si le produit n'est pas encore dans le panier
            $cart[$id] = []; // on initialise un tableau pour stocker les tailles et les quantités
        }

        if (!empty($cart[$id][$size])) { // si la taille du produit est déjà dans le panier
            $cart[$id][$size]++;// on augmente la quantité de 1 
        } else {
            $cart[$id][$size] = 1; // sinon on ajoute le produit au panier avec une quantité de 1
        }

        $session->set('cart', $cart); // on enregistre le panier dans la session
     
         return $this->redirectToRoute('cart'); // on redirige vers la page du panier
    }


    #[Route('/cart/increase/{id}/{size}', name: 'cart_increase')]
    public function increase($id, $size, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []); // récupère le panier de la session, ou un tableau vide si il n'existe pas

        if (!empty($cart[$id][$size])) { // si la taille du produit est déjà dans le panier 
            $cart[$id][$size]++; // on augmente la quantité de 1
        }

        $session->set('cart', $cart); // on enregistre le panier dans la session
     
         return $this->redirectToRoute('cart'); // on redirige vers la page du panier
    }


    #[Route('/cart/decrease/{id}/{size}', name: 'cart_decrease')]
    public function decrease($id,$size, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []); // récupère le panier de
        if (!empty($cart[$id][$size])) { // si la taille du produit est déjà dans le panier
            if ($cart[$id][$size] > 1) { // si la quantité est supérieure à 1
                $cart[$id][$size]--; // on diminue la quantité de 1
            } else {
                unset($cart[$id][$size]); // sinon on supprime la taille du produit du panier
            }
            //n'ettoie si plus aucune taille du produit n'est dans le panier
            if (empty($cart[$id])) { // si aucune taille du produit n'est dans le panier
                unset($cart[$id]); // on supprime le produit du panier
            }
        }
        $session->set('cart', $cart); // on enregistre le panier dans la session

            return $this->redirectToRoute('cart'); // on redirige vers la page du panier
    }

            

            

    //route pour supprimer un produit du panier
    #[Route('/cart/remove/{id}/{size}', name: 'cart_remove')]
    public function remove($id,$size, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []); // récupère le panier de
        if (!empty($cart[$id][$size])) { // si la taille du produit est déjà dans le panier
            unset($cart[$id][$size]); // on supprime la taille du produit du panier
            
            //n'ettoie si plus aucune taille du produit n'est dans le panier
            if (empty($cart[$id])) { // si aucune taille du produit n'est dans le panier
                unset($cart[$id]); // on supprime le produit du panier
            }
        }
        $session->set('cart', $cart); // on enregistre le panier dans la session
     
         return $this->redirectToRoute('cart'); // on redirige vers la page du panier


    }
    



}
