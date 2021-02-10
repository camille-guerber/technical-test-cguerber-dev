<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Task;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    private UserRepository $userRepository;
    private CustomerRepository $customerRepository;

    public function __construct(
        UserRepository $userRepository,
        CustomerRepository $customerRepository
    ) {
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CustomerFixtures::class
        ];
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $customers = $this->customerRepository->findAll();
        $users = $this->userRepository->findAll();

        for($i = 0; $i < 150; $i++) {
            $task = new Task();
            $task->setCreatedAt($faker->dateTimeBetween('-1 year'));

            $task->setUser(
                $faker->optional(0.7)->randomElement($users)
            );

            $task->setCustomer(
                $faker->randomElement($customers)
            );

            $task->setClosed($faker->boolean);

            if($task->getClosed()) {
                $task->setClosedAt($faker->dateTimeBetween($task->getCreatedAt()));
            }

            $task->setLabel($faker->sentence);

            $manager->persist($task);
        }

        $manager->flush();
    }
}
