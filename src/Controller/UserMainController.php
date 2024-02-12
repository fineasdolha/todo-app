<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tasks;
use DateTime;
use Symfony\Component\Validator\Constraints\Date;

class UserMainController extends AbstractController
{
    public function __construct(
        private RequestStack $requestStack,
    ) {

    }    
    
    #[Route('/{_locale}/user', name: 'app_user_main')]
    public function index(Request $request, UserInterface $user, EntityManagerInterface $entityManager): Response
    {   
        $session = $this->requestStack->getSession();
        $session->set('user_id', $user->getId());
        $session->set('user_username', $user->getUsername());
        $session->set('user_roles', $user->getRoles());
        $session->set('user_isVerified', $user->IsVerified());
        $session->set('user_firstName', $user->getFirstName());
        $session->set('user_lastName',$user->getLastName());
        $session->set('user_photo',$user ->getphotoFileName());
        $today = new DateTime('now');
        $tasks = $entityManager->getRepository(Tasks::class)->findBy(['utilisateur' => $this->getUser(),
                                                                       'duedate' =>   $today ]);
        return $this->render('user_main/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }
}
