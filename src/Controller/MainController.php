<?php

namespace App\Controller;

use App\DataFixtures\AppFixtures;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures;

class MainController extends AbstractController
{
    #[Route('/{_locale}', name: 'app_main')]
    public function index(EntityManagerInterface $entityManager): Response
    {   

        $users = $entityManager ->getRepository(User::class)->findAll();
        
        return $this->render('main/index.html.twig', [
            'users' => count($users),
        ]);
    }
}
