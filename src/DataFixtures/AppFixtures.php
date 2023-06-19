<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\ContainsIngredient;
use App\Entity\Department;
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

        $faker = \Faker\Factory::create('fr_FR');
        $faker->addProvider(new \FakerRestaurant\Provider\fr_FR\Restaurant($faker));

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

        $recipes = [
            ["Salade de pastèque, feta et oignon", 0, 15, "ÉTAPE 1
            Couper finement l'oignon et le laisser mariner dans le jus de citron.
            ÉTAPE 2
            Pendant ce temps, découper la pastèque et la feta en dés.
            ÉTAPE 3
            Mettre le tout dans un grand saladier, ajouter l'oignon avec le jus de citron, quelques feuilles de menthe ciselée et du persil.
            ÉTAPE 4
            Arroser d'un filet d'huile d'olive.", 1, 'pasteque.jpg', $allCategories[0], 4],
            ["Tartelette pommes de terre, chèvre et pancetta
            ", 45, 15, "ÉTAPE 1
            Détailler votre pâte brisée à la dimension de votre moule à tartelettes pour la précuire une dizaine de minutes à 180°C.
            ÉTAPE 2
            Dans une casserole, plonger des lamelles de pommes de terre (3mm) dans un mélange eau et lait et faites cuire environ 10 min.
            ÉTAPE 3
            Placer des tranches de pommes de terre dans les moules avec la pâte précuite. Ajouter des morceaux de fromage de chèvre puis une tranche de pancetta.
            ÉTAPE 4
            Battez les œufs et la crème. Verser la préparation dans les moules.
            ÉTAPE 5
            Enfournez environ 15 min au four à 180°.", 1, 'tartelette.jpg', $allCategories[0], 4],
            ["Lasagnes à la bolognaise", 95, 30, "ÉTAPE 1
            Faire revenir gousses hachées d'ail et les oignons émincés dans un peu d'huile d'olive.
            ÉTAPE 2
            Ajouter la carotte et la branche de céleri hachée puis la viande et faire revenir le tout.
            ÉTAPE 3
            Au bout de quelques minutes, ajouter le vin rouge. Laisser cuire jusqu'à évaporation.
            ÉTAPE 4
            Ajouter la purée de tomates, l'eau et les herbes. Saler, poivrer, puis laisser mijoter à feu doux 45 minutes.
            ÉTAPE 5
            Préparer la béchamel : faire fondre 100 g de beurre.
            ÉTAPE 6
            Hors du feu, ajouter la farine d'un coup.
            ÉTAPE 7
            Remettre sur le feu et remuer avec un fouet jusqu'à l'obtention d'un mélange bien lisse.
            ÉTAPE 8
            Ajouter le lait peu à peu.
            ÉTAPE 9
            Remuer sans cesse, jusqu'à ce que le mélange s'épaississe.
            ÉTAPE 10
            Ensuite, parfumer avec la muscade, saler, poivrer. Laisser cuire environ 5 minutes, à feu très doux, en remuant. Réserver.
            ÉTAPE 11
            Préchauffer le four à 200°C (thermostat 6-7). Huiler le plat à lasagnes. Poser une fine couche de béchamel puis des feuilles de lasagnes, de la bolognaise, de la béchamel et du parmesan. Répéter l'opération 3 fois de suite.
            ÉTAPE 12
            Sur la dernière couche de lasagnes, ne mettre que de la béchamel et recouvrir de fromage râpé. Parsemer quelques noisettes de beurre.
            ÉTAPE 13
            Enfourner pour environ 25 minutes de cuisson.", 2, 'lasagne.jpg', $allCategories[1], 8],
            ['Gâteau au chocolat fondant rapide', 30, 10, "ÉTAPE 1
            Préchauffez votre four à 180°C (thermostat 6). Dans une casserole, faites fondre le chocolat et le beurre coupé en morceaux à feu très doux.
            ÉTAPE 2
            Dans un saladier, ajoutez le sucre, les oeufs, la farine. Mélangez.
            ÉTAPE 3
            Ajoutez le mélange chocolat/beurre. Mélangez bien.
            ÉTAPE 4
            Beurrez à l'aide d'une feuille de papier essuie-tout et farinez votre moule puis y versez la pâte à gâteau.
            ÉTAPE 5
            Faites cuire au four environ 20 minutes.
            ÉTAPE 6
            A la sortie du four le gâteau ne paraît pas assez cuit. C'est normal, laissez-le refroidir puis démoulez- le.", 1, 'chocolat.jpg', $allCategories[2], 6]

        ];

        $allRecipes = [];

        foreach ($recipes as $recipe) {
            $newRecipe = new Recipe();
            $newRecipe->setTitle($recipe[0]);
            $newRecipe->setCookingDuration($recipe[1]);
            $newRecipe->setSetupDuration($recipe[2]);
            $newRecipe->setStep($recipe[3]);
            $newRecipe->setDifficulty($recipe[4]);
            $newRecipe->setPicture('images/recipe-picture/' . $recipe[5]);
            $newRecipe->setCategory($recipe[6]);
            $newRecipe->setPortions($recipe[7]);
            $manager->persist($newRecipe);

            $allRecipes[] = $newRecipe;
        }

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(150);
        $newContainsIngredient->setRecipe($allRecipes[0]);
        $newContainsIngredient->setIngredient($allIngredients[0]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(1);
        $newContainsIngredient->setRecipe($allRecipes[0]);
        $newContainsIngredient->setIngredient($allIngredients[1]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(5);
        $newContainsIngredient->setRecipe($allRecipes[0]);
        $newContainsIngredient->setIngredient($allIngredients[2]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(50);
        $newContainsIngredient->setRecipe($allRecipes[0]);
        $newContainsIngredient->setIngredient($allIngredients[3]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(1);
        $newContainsIngredient->setRecipe($allRecipes[0]);
        $newContainsIngredient->setIngredient($allIngredients[4]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(1);
        $newContainsIngredient->setRecipe($allRecipes[0]);
        $newContainsIngredient->setIngredient($allIngredients[5]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(1);
        $newContainsIngredient->setRecipe($allRecipes[1]);
        $newContainsIngredient->setIngredient($allIngredients[6]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(3);
        $newContainsIngredient->setRecipe($allRecipes[1]);
        $newContainsIngredient->setIngredient($allIngredients[7]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(20);
        $newContainsIngredient->setRecipe($allRecipes[1]);
        $newContainsIngredient->setIngredient($allIngredients[8]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(200);
        $newContainsIngredient->setRecipe($allRecipes[1]);
        $newContainsIngredient->setIngredient($allIngredients[9]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(2);
        $newContainsIngredient->setRecipe($allRecipes[1]);
        $newContainsIngredient->setIngredient($allIngredients[10]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(30);
        $newContainsIngredient->setRecipe($allRecipes[1]);
        $newContainsIngredient->setIngredient($allIngredients[11]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(4);
        $newContainsIngredient->setRecipe($allRecipes[1]);
        $newContainsIngredient->setIngredient($allIngredients[12]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(1);
        $newContainsIngredient->setRecipe($allRecipes[2]);
        $newContainsIngredient->setIngredient($allIngredients[13]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(3);
        $newContainsIngredient->setRecipe($allRecipes[2]);
        $newContainsIngredient->setIngredient($allIngredients[1]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(1);
        $newContainsIngredient->setRecipe($allRecipes[2]);
        $newContainsIngredient->setIngredient($allIngredients[14]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(800);
        $newContainsIngredient->setRecipe($allRecipes[2]);
        $newContainsIngredient->setIngredient($allIngredients[15]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(70);
        $newContainsIngredient->setRecipe($allRecipes[2]);
        $newContainsIngredient->setIngredient($allIngredients[16]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(125);
        $newContainsIngredient->setRecipe($allRecipes[2]);
        $newContainsIngredient->setIngredient($allIngredients[17]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(100);
        $newContainsIngredient->setRecipe($allRecipes[2]);
        $newContainsIngredient->setIngredient($allIngredients[18]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(200);
        $newContainsIngredient->setRecipe($allRecipes[3]);
        $newContainsIngredient->setIngredient($allIngredients[19]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(100);
        $newContainsIngredient->setRecipe($allRecipes[3]);
        $newContainsIngredient->setIngredient($allIngredients[20]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(50);
        $newContainsIngredient->setRecipe($allRecipes[3]);
        $newContainsIngredient->setIngredient($allIngredients[18]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(100);
        $newContainsIngredient->setRecipe($allRecipes[3]);
        $newContainsIngredient->setIngredient($allIngredients[21]);
        $manager->persist($newContainsIngredient);

        $newContainsIngredient = new ContainsIngredient();
        $newContainsIngredient->setQuantity(3);
        $newContainsIngredient->setRecipe($allRecipes[3]);
        $newContainsIngredient->setIngredient($allIngredients[10]);
        $manager->persist($newContainsIngredient);


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

        $manager->flush();
    }
}