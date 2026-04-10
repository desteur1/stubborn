<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\SweatshirtRepository;
use App\Entity\Sweatshirt;
use App\Form\SweatshirtType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

final class AdminSweatshirtController extends AbstractController
{
    #[Route('/admin/sweatshirt', name: 'app_admin_sweatshirt')]
    public function index(SweatshirtRepository $repo): Response
    {
        return $this->render('admin_sweatshirt/index.html.twig', [
            'sweatshirts' => $repo->findAll()
        ]);
    }

    #[Route('/admin/sweatshirts/new', name: 'admin_sweat_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
   {
        $sweat = new Sweatshirt();
        $form = $this->createForm(SweatshirtType::class, $sweat);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

         $imageFile = $form->get('imageFile')->getData();// Récupérer le fichier uploadé
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension(); // Générer un nom de fichier unique

                $imageFile->move($this->getParameter('images_directory'), $newFilename); // Enregistrer le nom du fichier dans l'entité
                
                $sweat->setImage($newFilename);
            }
            
             $em->persist($sweat);
            $em->flush();



            return $this->redirectToRoute('app_admin_sweatshirt');
        }

        return $this->render('admin_sweatshirt/new.html.twig', [
            'form' => $form->createView()
        ]);
   }

    #[Route('/admin/sweatshirts/edit/{id}', name: 'admin_sweat_edit')]
    public function edit(Sweatshirt $sweat, Request $request, EntityManagerInterface $em): Response
  {
        $form = $this->createForm(SweatshirtType::class, $sweat);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_admin_sweatshirt');
        }

        return $this->render('admin_sweatshirt/edit.html.twig', [
            'form' => $form->createView(),
            'sweatshirt' => $sweat
        ]);
   }
   
    #[Route('/admin/sweatshirts/delete/{id}', name: 'admin_sweat_delete')]
    public function delete(Sweatshirt $sweat, EntityManagerInterface $em): Response
    {
        $em->remove($sweat);
        $em->flush();

        return $this->redirectToRoute('app_admin_sweatshirt');
    }
}
