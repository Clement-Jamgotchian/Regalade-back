<?php

namespace App\DataFixtures;

use App\Entity\Allergen;
use App\Entity\Category;
use App\Entity\ContainsIngredient;
use App\Entity\Department;
use App\Entity\Diet;
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
        $allergens = ['Arachide', 'Céleri', 'Crustacés', 'Gluten', 'Fruits à coque', 'Lait', 'Oeuf', 'Poisson', 'Sézame', 'Sulfites'];

        $allAllergens = [];
        foreach ($allergens as $allergen) {
            $newAllergy = new Allergen();
            $newAllergy->setName($allergen);
            $manager->persist($newAllergy);

            $allAllergens[] = $newAllergy;
        }

        $diets = ["Végétarien", "Végan", "Fléxitarien", "Hypocalorique"];

        $allDiets = [];
        foreach ($diets as $diet) {
            $newDiet = new Diet();
            $newDiet->setName($diet);
            $manager->persist($newDiet);

            $allDiets[] = $newDiet;
        }

        $categories = ['Entrée', "Plat", "Dessert"];

        $allCategories = [];
        foreach ($categories as $category) {
            $newCategory = new Category();
            $newCategory->setTitle($category);
            $manager->persist($newCategory);

            $allCategories[] = $newCategory;

        }

        $departments = ["Boucherie/Charcuterie", "Poissonnerie", "Boissons", "Fruits et légumes", "Frais", "Fromage", "Epicerie", "Boulangerie", "Surgelé"];

        $allDepartments = [];

        foreach ($departments as $department) {
            $newDepartment = new Department();
            $newDepartment->setName($department);
            $manager->persist($newDepartment);

            $allDepartments[] = $newDepartment;

        } 

        $ingredients = [
            ["Feta", 'gr', $allDepartments[5], 1],
            ["Oignon", 'pce', $allDepartments[3], 0],
            ["Huile d'olive", 'cl', $allDepartments[6], 0],
            ["Persil", 'gr', $allDepartments[6], 0],
            ["Pastèque", 'pce', $allDepartments[3], 0],
            ["Citron", 'pce', $allDepartments[3], 1],
            ["Pate brisée", "pce", $allDepartments[4], 1],
            ["Pomme de terre", "pce", $allDepartments[3], 0],
            ["Lait", "cl", $allDepartments[4], 1],
            ["Fromage de chèvre", "gr", $allDepartments[5], 1],
            ["Oeuf", "pce", $allDepartments[6], 1],
            ["Crème", "cl", $allDepartments[5], 1],
            ["Pancetta", "pce", $allDepartments[0], 1],
            ["Lasagnes", "paquet", $allDepartments[6], 0],
            ["Carotte", "pce", $allDepartments[3], 0],
            ["Sauce tomate", "gr", $allDepartments[5], 0],
            ["Fromage rapé", "gr", $allDepartments[5], 1],
            ["Parmesan", "gr", $allDepartments[5], 1],
            ["Farine", "gr", $allDepartments[6], 0],
            ["Chocolat patissier", 'gr', $allDepartments[6],0],
            ["Beurre", 'gr', $allDepartments[4], 1],
            ["Sucre en poudre", 'gr', $allDepartments[6], 0]
        ];

        $allIngredients = [];
        foreach ($ingredients as $ingredient) {
            $new = new Ingredient();
            $new->setName($ingredient[0]);
            $new->setUnit($ingredient[1]);
            $new->setDepartment($ingredient[2]);
            $new->setIsCold($ingredient[3]);

            $manager->persist($new);

            $allIngredients[] = $new;
        }

        /** @var User[] */
        $users = [];

        $admins = ['etienne', 'leslie', 'clement', 'gael', 'corentin'];

        foreach ($admins as $admin) {
            $newAdmin = new User();
            $newAdmin->setEmail( $admin."@regalade.com");
            $newAdmin->setPassword('$2y$13$iHwEbpb8kYW0Q90T7g6dhe3O5T9UJ9VDwGmlCeMhL53L9juxe33lW');
            $newAdmin->setNickname($admin);
            $newAdmin->setRoles(['ROLE_ADMIN']);
            $manager->persist($newAdmin);
            $users[] = $newAdmin;
    
            $memberAdmin = new Member();
            $memberAdmin->setNickname($newAdmin->getNickname());
            $memberAdmin->setIsAdult(true);
            $memberAdmin->setUser($newAdmin);
            $manager->persist($memberAdmin);
        }

        $manager->flush();
    }
}