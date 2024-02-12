<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CategoryType;
use App\Entity\Categories;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\HttpFoundation\Request;




class CategoriesController extends AbstractController
{
    #[Route('/categories', name: 'app_categories')]
    public function index(): Response
    {
        return $this->render('categories/index.html.twig', [
            'controller_name' => 'CategoriesController',
        ]);
    }

    #[Route('/{_locale}/tasks/add-category', name: 'app_addcategory')]
         
    public function new(Request $request, EntityManagerInterface $entity) 
    {
        // creates a task object
        $category = new Categories();

        $form = $this->createForm(CategoryType::class, $category);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entity->persist($category);
            $entity->flush();

            return $this->redirectToRoute('app_user_main');
        }

        return $this->render('categories/new.html.twig', [
            'form' => $form
        ]);

    
    }


}


