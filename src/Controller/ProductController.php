<?php

namespace App\Controller;


// Import du repository pour accéder aux données en base
use App\Repository\SweatshirtRepository;

// ancienne syntaxe pour les routes
// use Symfony\Component\Routing\Annotation\Route;

// Permet de définir les routes avec #[Route]
use Symfony\Component\Routing\Attribute\Route;

// Classe de base avec des méthodes utiles (render, etc.)
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// Déclaration du contrôleur
class ProductController extends AbstractController
{
    // Route pour afficher tous les produits
    #[Route('/products', name: 'products')]
    public function index(SweatshirtRepository $repo)
    {
        // Récupère tous les produits depuis la base de données
        $products = $repo->findAll();

        // Envoie les produits à la vue Twig
        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    // Route pour afficher un produit spécifique via son ID
    #[Route('/product/{id}', name: 'product_show')]
    public function show($id, SweatshirtRepository $repo)
    {
        // Récupère un seul produit grâce à son ID
        $product = $repo->find($id);

        // Envoie le produit à la vue Twig
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }
}