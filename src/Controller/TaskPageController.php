<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Task;
use App\Form\TaskFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


final class TaskPageController extends AbstractController
{
	#[Route('/tasks/new', name: 'app_tasks_new')]
	public function new(Request $request, EntityManagerInterface $em): Response
	{
			$task = new Task();
			$form = $this->createForm(TaskFormType::class, $task);
			$form->handleRequest($request);
	
			if ($form->isSubmitted() && $form->isValid()) {
					$task->setCreatedAt(new \DateTimeImmutable());
					$task->setUser($this->getUser());
	
					$em->persist($task);
					$em->flush();
	
					$this->addFlash('success', 'Tâche créée avec succès !');
	
					return $this->redirectToRoute('app_tasks_new');
			}
	
			return $this->render('task/new.html.twig', [
					'taskForm' => $form,
			]);
	}
	
}
