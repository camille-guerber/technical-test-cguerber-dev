<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CustomerFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for($i = 0; $i < 15; $i++) {
            $customer = new Customer();
            $customer->setLabel($faker->company);

            $manager->persist($customer);
        }

        $manager->flush();
    }
}
