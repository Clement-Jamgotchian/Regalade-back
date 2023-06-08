<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\ContainsIngredient;
use App\Entity\Department;
use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker = \Faker\Factory::create('fr_FR');
        $faker->addProvider(new \FakerRestaurant\Provider\fr_FR\Restaurant($faker));

        $departments = ["Frais", "Fruits et légumes", "Boucherie"];

        $allDepartments = [];

        foreach ($departments as $department) {
            $newDepartment = new Department();
            $newDepartment->setName($department);
            $manager->persist($newDepartment);

            $allDepartments[] = $newDepartment;

        } 

        $allIngredients = [];

        for ($i=0; $i < 5; $i++) { 
            $ingredient = new Ingredient();
            $ingredient->setName($faker->unique()->dairyName());
            $ingredient->setUnit('cl');
            $ingredient->setDepartment($allDepartments[0]);
            $ingredient->setIsCold(1);
            $manager->persist($ingredient);

            $allIngredients[] = $ingredient;
        }

        for ($i=0; $i < 15; $i++) { 
            $ingredient = new Ingredient();
            $ingredient->setName($faker->unique()->vegetableName());
            $ingredient->setUnit('/');
            $ingredient->setDepartment($allDepartments[1]);
            $ingredient->setIsCold(0);
            $manager->persist($ingredient);

            $allIngredients[] = $ingredient;
        }

        for ($i=0; $i < 4; $i++) { 
            $ingredient = new Ingredient();
            $ingredient->setName($faker->unique()->meatName());
            $ingredient->setUnit('gr');
            $ingredient->setDepartment($allDepartments[2]);
            $ingredient->setIsCold(1);
            $manager->persist($ingredient);
        }

        $categories = ['Entrée', "Plat", "Dessert"];

        $allCategories = [];
        foreach ($categories as $category) {
            $newCategory = new Category();
            $newCategory->setTitle($category);
            $manager->persist($newCategory);

            $allCategories[] = $newCategory;

        }

        $allRecipes = [];

        for ($i=0; $i < 11; $i++) { 
            $newRecipe = new Recipe();
            $newRecipe->setTitle($faker->unique()->foodName());
            $newRecipe->setDescription($faker->realText(50));
            $newRecipe->setCookingDuration($faker->randomNumber(2));
            $newRecipe->setSetupDuration($faker->randomNumber(2));
            $newRecipe->setStep($faker->realText(250));
            $newRecipe->setDifficulty(mt_rand(1,3));
            $manager->persist($newRecipe);

            $allRecipes[] = $newRecipe;
        }

        foreach ($allRecipes as $recipe) {

            $randomNb = mt_rand(2, 6);
            for ($i=1; $i <= $randomNb; $i++) {

                $newContainsIngredient = new ContainsIngredient();
                $newContainsIngredient->setQuantity(mt_rand(1, 500));
                $newContainsIngredient->setRecipe($recipe);
                $newContainsIngredient->setIngredient($allIngredients[mt_rand(0, count($allIngredients)-1)]);

                $manager->persist($newContainsIngredient);

            }

            $recipe->setCategory($allCategories[mt_rand(0, count($allCategories)-1)]);
            $recipe->setRating(mt_rand(0,50) / 10);
        }

        $newAdmin = new User();
        $newAdmin->setEmail("admin@admin.com");
        $newAdmin->setPassword('$2y$13$iHwEbpb8kYW0Q90T7g6dhe3O5T9UJ9VDwGmlCeMhL53L9juxe33lW');
        $newAdmin->setNickname('admin');
        $newAdmin->setRoles(['ROLE_ADMIN']);
        $manager->persist($newAdmin);

        $newUser = new User();
        $newUser->setEmail("user@user.com");
        $newUser->setPassword('$2y$13$iHwEbpb8kYW0Q90T7g6dhe3O5T9UJ9VDwGmlCeMhL53L9juxe33lW');
        $newUser->setNickname('user');
        $newUser->setRoles(['ROLE_USER']);
        $manager->persist($newUser);

        $newUser->addRecipe($allRecipes[0]);


        $manager->flush();
    }
}