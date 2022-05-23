<?php

namespace App\Controller;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CrudListController extends AbstractController
{
    #[Route('/', name: 'app_crud_list')]
    public function index(EntityManagerInterface $em): Response
    {
        $tasks = $em->getRepository(Task::class)->findBy([], ['id' => 'DESC']);
        return $this->render('crud_list/index.html.twig', ['tasks' => $tasks]);
    }

    #[Route('/create', name: 'create_task', methods: ['POST'])]
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        $title = trim($request->get("title"));
        if (empty($title))
            return $this->redirectToRoute('app_crud_list');

        $entityManager = $doctrine->getManager();
        $task = new Task;
        $task->setTitle($title);
        $entityManager->persist($task);
        $entityManager->flush();

        return $this->redirectToRoute('app_crud_list');
    }

    #[Route('/update/{id}', name: 'update_task')]
    public function update($id, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $task = $entityManager->getRepository(Task::class)->find($id);
        $task->setStatus(!$task->getStatus());
        $entityManager->flush();
        return $this->redirectToRoute('app_crud_list');
    }

    #[Route('/delete/{id}', name: 'delete_task')]
    public function delete($id, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $id = $entityManager->getRepository(Task::class)->find($id);
        $entityManager->remove($id);
        $entityManager->flush();
        return $this->redirectToRoute('app_crud_list');
    }
}