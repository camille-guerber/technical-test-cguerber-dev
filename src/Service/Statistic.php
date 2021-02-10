<?php


namespace App\Service;

use App\Repository\TaskRepository;

class Statistic
{
    /**
     * @var TaskRepository
     */
    private TaskRepository $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function getCountUnassignedTasks()
    {
        return count($this->taskRepository->getUnassignedTasks());
    }

    public function getCountOpenedTasks()
    {
        return count($this->taskRepository->getOpenedTasks());
    }

    public function getCountOwnedOpenedTasks()
    {
        return count($this->taskRepository->getOwnedOpenedTasks());
    }

    /**
     * @return array
     */
    public function dashboardStats(): array
    {
        $stats = [
            'opened' => 0,
            'unassigned' => 0,
            'owned' => 0
        ];

        $stats['opened'] = $this->getCountOpenedTasks();
        $stats['unassigned'] = $this->getCountUnassignedTasks();
        $stats['owned'] = $this->getCountOwnedOpenedTasks();

        return $stats;
    }
}