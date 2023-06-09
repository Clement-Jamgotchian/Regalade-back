<?php

namespace App\DataFixtures;

use App\Entity\Cart;
use App\Entity\Category;
use App\Entity\ContainsIngredient;
use App\Entity\Department;
use App\Entity\Fridge;
use App\Entity\RecipeList;
use App\Entity\Ingredient;
use App\Entity\Member;
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

        $departments = ["Boucherie/Charcuterie", "Poissonnerie", "Boissons", "Fruits et légumes", "Frais", "Fromage", "Epicerie", "Boulangerie", "Surgelé"];

        $allDepartments = [];

        foreach ($departments as $department) {
            $newDepartment = new Department();
            $newDepartment->setName($department);
            $manager->persist($newDepartment);

            $allDepartments[] = $newDepartment;

        } 

        $allIngredients = [];

        for ($i=0; $i < 4; $i++) { 
            $ingredient = new Ingredient();
            $ingredient->setName($faker->dairyName());
            $ingredient->setUnit('cl');
            $ingredient->setDepartment($allDepartments[4]);
            $ingredient->setIsCold(1);
            $manager->persist($ingredient);

            $allIngredients[] = $ingredient;
        }

        for ($i=0; $i < 20; $i++) { 
            $ingredient = new Ingredient();
            $ingredient->setName($faker->vegetableName());
            $ingredient->setUnit('pce');
            $ingredient->setDepartment($allDepartments[3]);
            $ingredient->setIsCold(0);
            $manager->persist($ingredient);

            $allIngredients[] = $ingredient;
        }

        for ($i=0; $i < 18; $i++) { 
            $ingredient = new Ingredient();
            $ingredient->setName($faker->vegetableName());
            $ingredient->setUnit('pce');
            $ingredient->setDepartment($allDepartments[3]);
            $ingredient->setIsCold(0);
            $manager->persist($ingredient);

            $allIngredients[] = $ingredient;
        }

        for ($i=0; $i < 6; $i++) { 
            $ingredient = new Ingredient();
            $ingredient->setName($faker->meatName());
            $ingredient->setUnit('gr');
            $ingredient->setDepartment($allDepartments[0]);
            $ingredient->setIsCold(1);
            $manager->persist($ingredient);

            $allIngredients[] = $ingredient;
        }

        for ($i=0; $i < 4; $i++) { 
            $ingredient = new Ingredient();
            $ingredient->setName($faker->sauceName());
            $ingredient->setUnit('gr');
            $ingredient->setDepartment($allDepartments[6]);
            $ingredient->setIsCold(0);
            $manager->persist($ingredient);

            $allIngredients[] = $ingredient;
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

        for ($i=0; $i < 15; $i++) { 
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

            $randomNb = mt_rand(2, 7);
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

        /** @var User[] */
        $users = [];

        $newAdmin = new User();
        $newAdmin->setEmail("admin@admin.com");
        $newAdmin->setPassword('$2y$13$iHwEbpb8kYW0Q90T7g6dhe3O5T9UJ9VDwGmlCeMhL53L9juxe33lW');
        $newAdmin->setNickname('admin');
        $newAdmin->setRoles(['ROLE_ADMIN']);
        $manager->persist($newAdmin);
        $users[] = $newAdmin;

        $memberAdmin = new Member();
        $memberAdmin->setNickname($newAdmin->getNickname());
        $memberAdmin->setIsAdult(true);
        $memberAdmin->setUser($newAdmin);
        $manager->persist($memberAdmin);

        for ($i=0; $i < 50; $i++) { 
            $newUser = new User();
            $newUser->setEmail($faker->email());
            $newUser->setPassword('$2y$13$iHwEbpb8kYW0Q90T7g6dhe3O5T9UJ9VDwGmlCeMhL53L9juxe33lW');
            $newUser->setNickname($faker->firstName());
            $newUser->setRoles(['ROLE_USER']);
            $manager->persist($newUser);
            $users[] = $newUser;
    
            $memberUser = new Member();
            $memberUser->setNickname($newUser->getNickname());
            $memberUser->setIsAdult(true);
            $memberUser->setUser($newUser);
            $manager->persist($memberUser);
        }

        foreach ($users as $user) {
            $randomNb = mt_rand(0, 4);
            for ($i=1; $i <= $randomNb; $i++) {
                $member = new Member();
                $member->setNickname($faker->firstName());
                $member->setIsAdult(true);
                $member->setUser($user);
                $manager->persist($member);
            }

            $random = mt_rand(1,5);
            for ($i=1; $i <= $random; $i++) {
                $user->addFavoriteRecipe($allRecipes[mt_rand(0, count($allRecipes)-1)]);
            }

            $random2 = mt_rand(1,6);
            for ($i=1; $i <= $random2; $i++) {
                $newRecipeList = new RecipeList();
                $newRecipeList->setRecipe($allRecipes[mt_rand(0, count($allRecipes)-1)]);
                $newRecipeList->setUser($user);
                $manager->persist($newRecipeList);
        
            }

            $random3 = mt_rand(0, 20);
            for ($i=1; $i <= $random3; $i++) {
                $cart = new Cart();
                $cart->setIngredient($allIngredients[mt_rand(0, count($allRecipes)-1)]);
                $cart->setQuantity(mt_rand(1, 500));
                $cart->setUser($user);
                $manager->persist($cart);
            }

            $random4 = mt_rand(0, 15);
            for ($i=1; $i <= $random4; $i++) {
                $fridge = new Fridge();
                $fridge->setIngredient($allIngredients[mt_rand(0, count($allRecipes)-1)]);
                $fridge->setQuantity(mt_rand(1, 500));
                $fridge->setUser($user);
                $manager->persist($fridge);
            }
        }

        $manager->flush();
    }
}