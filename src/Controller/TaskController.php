<?php


namespace App\Controller;


use App\Entity\Task;
use App\Form\Filter\TaskFilterType;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TaskController
 * @package App\Controller
 * @Route("/task")
 * @IsGranted("IS_AUTHENTICATED_FULLY")
 */
class TaskController extends AbstractController
{
    private TaskRepository $taskRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        TaskRepository $taskRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->taskRepository = $taskRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("", name="task", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response {

        $query = null;

        $filterForm = $this->createForm(TaskFilterType::class, []);

        $filterForm->handleRequest($request);

        $tasks = $this->taskRepository->pagination(
            $request->query->getInt('page', 1),
            $filterForm->getData() ?? []
        );

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'filterForm' => $filterForm->createView(),
        ]);
    }

    /**
     * @Route("/add", name="task_add")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response {
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            $this->addFlash('success', "The task has been created.");
            return $this->redirectToRoute('task');
        }

        return $this->render('task/add.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/edit/{task}", name="task_edit")
     * @param Request $request
     * @param Task $task
     * @return Response
     */
    public function edit(Request $request, Task $task): Response {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', "The task has been updated.");
            return $this->redirectToRoute('task');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task
        ]);
    }

    /**
     * @Route("/delete/{task}", name="task_delete")
     * @param Task $task
     * @return RedirectResponse
     */
    public function delete(Task $task): RedirectResponse {
        $this->entityManager->remove($task);
        $this->entityManager->flush();

        $this->addFlash('success', "The task has been deleted.");
        return $this->redirectToRoute('task');
    }

    /**
     * @Route("/close/{task}", name="task_close")
     * @param Task $task
     * @return RedirectResponse
     */
    public function close(Task $task): RedirectResponse {
        $task->setClosed(true);
        $task->setClosedAt(new \DateTime());

        $this->entityManager->flush();

        $this->addFlash('success', "The task has been completed");
        return $this->redirectToRoute('task');
    }
}