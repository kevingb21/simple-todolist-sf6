<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskFormType;
use App\Repository\TaskRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{

    /**
     * List all tasks
     *
     * @param TaskRepository $taskRepository
     * @return Response
     */
    #[Route('/', name: 'app_task')]
    public function index(TaskRepository $taskRepository): Response
    {
        $tasks = $taskRepository->findBy([
            'deleted' => false,
        ]);

        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
            'tasks' => $tasks
        ]);
    }
    
    /**
     * Create a new task
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/create', methods: ['GET', 'POST'], name: 'app_task_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response {
        $task = new Task();

        $form = $this->createForm(TaskFormType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setDeleted(false);
            $task->setCreatedAt(new \DateTime());
            $task->setUpdatedAt(new \DateTime());

            $entityManager->persist($task);
            $entityManager->flush();

            $this->addFlash('success', 'Task created');

            return $this->redirectToRoute('app_task');
        }

        return $this->render('task/create.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    /**
     * Update a task
     * 
     */
    #[Route('/{id}/update', methods: ['GET', 'POST'], name: 'app_task_edit')]
    public function update(Request $request, EntityManagerInterface $entityManager, Task $task): Response
    {
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Task updated');

            return $this->redirectToRoute('app_task_edit', ['id' => $task->getId()]);
        }

        return $this->render('task/update.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    /**
     * Delete a task
     */
    #[Route('/{id}/delete', name: 'app_task_delete')]
    public function delete(Request $request, EntityManagerInterface $entityManager, Task $task): Response
    {
        
        $task->setDeleted(true);
        $task->setUpdatedAt(new \DateTime());
        $entityManager->persist($task);
        $entityManager->flush();

        $this->addFlash('success', 'Task deleted');

        return $this->redirectToRoute('app_task');
    }
    
}
