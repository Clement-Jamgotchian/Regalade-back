<?php

namespace App\DataFixtures;

use App\Entity\Department;
use App\Entity\Ingredient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker = \Faker\Factory::create();
        $faker->addProvider(new \FakerRestaurant\Provider\fr_FR\Restaurant($faker));

        $departments = ["Frais", "Fruits et légumes", "Boucherie"];

        $allDepartments = [];

        foreach ($departments as $department) {
            $newDepartment = new Department();
            $newDepartment->setName($department);
            $manager->persist($newDepartment);

            $allDepartments[] = $newDepartment;

        } 

        for ($i=0; $i < 5; $i++) { 
            $ingredient = new Ingredient();
            $ingredient->setName($faker->unique()->dairyName());
            $ingredient->setUnit('cl');
            $ingredient->setDepartment($allDepartments[0]);
            $ingredient->setIsCold(1);
            $manager->persist($ingredient);
        }

        for ($i=0; $i < 15; $i++) { 
            $ingredient = new Ingredient();
            $ingredient->setName($faker->unique()->vegetableName());
            $ingredient->setUnit('/');
            $ingredient->setDepartment($allDepartments[1]);
            $ingredient->setIsCold(0);
            $manager->persist($ingredient);
        }

        for ($i=0; $i < 4; $i++) { 
            $ingredient = new Ingredient();
            $ingredient->setName($faker->unique()->meatName());
            $ingredient->setUnit('gr');
            $ingredient->setDepartment($allDepartments[2]);
            $ingredient->setIsCold(1);
            $manager->persist($ingredient);
        }

        $manager->flush();
    }
}
