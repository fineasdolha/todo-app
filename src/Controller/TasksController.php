<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Tasks;
use App\Form\FormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\JsonResponse;



class TasksController extends AbstractController
{

    #[Route('/{_locale}/tasks', name: 'app_tasks')]
    
    public function index(EntityManagerInterface $entityManager) : Response
    
    {   

        $tasks = $entityManager->getRepository(Tasks::class)->findBy(['utilisateur' => $this->getUser()]);
        
        return $this->render('tasks/index.html.twig', [
            'tasks' => $tasks
        ]);
    }
    
    #[Route('/{_locale}/tasks/add-tasks', name: 'app_addtask')]
         
    public function new(Request $request, EntityManagerInterface $entity) 
    {
        // creates a task object
        $task = new Tasks();

        $form = $this->createForm(FormType::class, $task);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUtilisateur($this->getUser());
            $task->setStatus('to-do');
            $task ->setCategory($form["category"]->getData());
            $entity->persist($task);
            $entity->flush();

            return $this->redirectToRoute('app_user_main');
        }

        return $this->render('tasks/new.html.twig', [
            'form' => $form
        ]);

    
    }

    #[Route('/{_locale}/tasks/status-done/{slug}', name: 'app_status_done')]

    public function changeStatus($slug, EntityManagerInterface $entityManager){
        $taskToUpdate = $entityManager->getRepository(Tasks::class)->find($slug);
        $taskToUpdate->setStatus('done');
        $entityManager->persist($taskToUpdate);
        $entityManager->flush();
        return $this->redirectToRoute('app_user_main');
    }

    #[Route('/{_locale}/tasks/status-not-done/{slug}', name: 'app_status_not_done')]

    public function changeStatusNotDone($slug, EntityManagerInterface $entityManager){
        $taskToUpdate = $entityManager->getRepository(Tasks::class)->find($slug);
        $taskToUpdate->setStatus('to-do');
        $entityManager->persist($taskToUpdate);
        $entityManager->flush();
        return $this->redirectToRoute('app_user_main');
    }

    #[Route('/{_locale}/tasks/delete/{slug}', name: 'app_delete')]

    public function delete($slug, EntityManagerInterface $entityManager){
        $taskToDelete = $entityManager->getRepository(Tasks::class)->find($slug);
        $entityManager->remove($taskToDelete);
        $entityManager->flush();
        $tasks = $entityManager->getRepository(Tasks::class)->findAll();
        return $this->redirectToRoute('app_user_main');
    }

    #[Route('/{_locale}/tasks/modify/{slug}', name: 'app_modify')]

    public function modify($slug, EntityManagerInterface $entityManager,Request $request) {
        $taskToModify = $entityManager->getRepository(Tasks::class)->find($slug);
        if (!$taskToModify) {
            throw $this->createNotFoundException(
                'No task found for id '.$slug
            );
        }
 
        

        $form = $this->createForm(FormType::class, $taskToModify);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $entityManager->persist($taskToModify);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_main');
        }

        return $this->render('tasks/new.html.twig', [
            'form' => $form
        ]);

    }

    #[Route('/{_locale}/api', name: 'app_api_calendar')]
    
    public function api(EntityManagerInterface $entityManager) : JsonResponse
    
    {   
       



        $tasks = $entityManager->getRepository(Tasks::class)->findBy(['utilisateur' => $this->getUser()]);
        $data = [];
        foreach ($tasks as $task) {
            $date = $task->getDuedate()->format('Y-m-d');
            $category =$task->getCategory()->__toString();
            $status =$task->getStatus();
            if($status== 'done'){
                $color='green';
            }else {
                $color='red';
            } 
            $data[] = [
            'id' => $task->getId(),
            'title' => $task->getName(),
            'description' => $task->getDescription(),
            'start' => $date,
            'priority' => $task->getPriority(),
            'category' => $category,
            'status' => $status,
            'backgroundColor' => '#fff',
            'textColor' => $color,
            'borderColor' => $color,
            'className' => "text-center"
            ];
        }
        return new JsonResponse($data, Response::HTTP_OK);
        
    }

    #[Route('/{_locale}/calendar', name: 'app_calendar')]
    
    public function showCalendar(EntityManagerInterface $entityManager) : Response
    
    {       
        return $this->render('tasks/calendar.html.twig');
    }
    


}
