<?php

namespace App\Controller;


// Import du repository pour accéder aux données en base
use App\Repository\SweatshirtRepository;

// ancienne syntaxe pour les routes
// use Symfony\Component\Routing\Annotation\Route;

// Permet de définir les routes avec #[Route]
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\HttpFoundation\Request;

// Classe de base avec des méthodes utiles (render, etc.)
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// Déclaration du contrôleur
class ProductController extends AbstractController
{
    // Route pour afficher tous les produits
    #[Route('/products', name: 'products')]
    public function index(Request $request, SweatshirtRepository $repo)
    {
        $range = $request->query->get('range');

        switch ($range) {
            case '1':
                $products = $repo->findByPriceRange(10, 29);
                break;
            case '2':
                $products = $repo->findByPriceRange(29, 35);
                break;
            case '3':
                $products = $repo->findByPriceRange(35, 50);
                break;
            default:
                $products = $repo->findAll();
                break;
        }

        // Envoie les produits à la vue Twig
        return $this->render('product/index.html.twig', [
            'products' => $products,
            'currentRange' => $range
        ]);
    }

    // Route pour afficher un produit spécifique via son ID
    #[Route('/product/{id}', name: 'product_show')]
    public function show(int $id, SweatshirtRepository $repo)
    {
        // Récupère un seul produit grâce à son ID
        $product = $repo->find($id);

        // Envoie le produit à la vue Twig
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }
}