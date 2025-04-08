<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\TaskFormType;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;


#[Route('/api/tasks')]
class TaskController extends AbstractController
{
	#[Route('', methods: ['GET'])]
	/**
	 * Récupère les tâches de l'utilisateur connecté
	 *
	 * @OA\Response(
	 *     response=200,
	 *     description="Liste des tâches",
	 *     @OA\JsonContent(
	 *         type="array",
	 *         @OA\Items(ref=@Model(type=Task::class, groups={"task:read"}))
	 *     )
	 * )
	 * @OA\Tag(name="Tasks")
	 */
    public function index(TaskRepository $taskRepository): JsonResponse
    {
        $tasks = $taskRepository->findAll();

        return $this->json($tasks, 200, [], ['groups' => 'task:read']);
    }

    #[Route('', methods: ['POST'])]
		/**
     * Crée une nouvelle tâche
     *
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="title", type="string"),
     *         @OA\Property(property="description", type="string"),
     *         @OA\Property(property="status", type="string", example="todo")
     *     )
     * )
     * @OA\Response(
     *     response=201,
     *     description="Tâche créée",
     *     @OA\JsonContent(ref=@Model(type=Task::class, groups={"task:read"}))
     * )
     * @OA\Tag(name="Tasks")
     */
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $task = new Task();
        $task->setTitle($data['title'] ?? '');
        $task->setDescription($data['description'] ?? null);
        $task->setStatus($data['status'] ?? 'todo');
        $task->setCreatedAt(new \DateTimeImmutable());
        // TODO : ajouter jwrt
				$task->setUser($em->getRepository(\App\Entity\German::class)->find(1));


        $em->persist($task);
        $em->flush();

        return $this->json($task, 201, [], ['groups' => 'task:read']);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request, TaskRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $task = $repo->find($id);
        if (!$task) {
            return $this->json(['message' => 'Task not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $task->setTitle($data['title'] ?? $task->getTitle());
        $task->setDescription($data['description'] ?? $task->getDescription());
        $task->setStatus($data['status'] ?? $task->getStatus());

        $em->flush();

        return $this->json($task, 200, [], ['groups' => 'task:read']);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, TaskRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $task = $repo->find($id);
        if (!$task) {
            return $this->json(['message' => 'Task not found'], 404);
        }

        $em->remove($task);
        $em->flush();

        return $this->json(null, 204);
    }

		
}
