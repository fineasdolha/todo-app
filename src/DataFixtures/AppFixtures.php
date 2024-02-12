<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR'); 
        $users = Array();
         for ($i = 0; $i < 30; $i++) {
            $users[$i] = new User();
            $users[$i]->setUsername($faker->email);
            $users[$i]->setFirstName($faker->firstName);
            $users[$i]->setLastName($faker->firstName);
            $users[$i]->setPassword('123456');

            $manager->persist($users[$i]);
        }
        
        $manager->flush();
    }
}
