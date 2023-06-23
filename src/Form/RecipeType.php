<?php

namespace App\Form;

use App\Entity\Allergen;
use App\Entity\Category;
use App\Entity\ContainsIngredient;
use App\Entity\Diet;
use App\Entity\Ingredient;
use App\Entity\Recipe;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ["label" => "Titre", "attr" => ["class" => "bg-primary", "placeholder" => "Le titre de la recette"]])
            ->add('description', TextareaType::class, ["label" => "Description", "attr" => ["class" => "bg-primary", "placeholder" => "Description (pas obligatoire)"]] )
            ->add('pictureFile', VichImageType::class, ["label" => "Illustration", "attr" => ["class" => "bg-primary"], "allow_delete" => true, "delete_label" => "Supprimer l'image", "download_link" => false])
            ->add('cookingDuration', IntegerType::class, ["label" => "Temps de cuisson (en minutes)", "attr" => ["class" => "bg-primary", "value" => 10]])
            ->add('setupDuration', IntegerType::class, ["label" => "Temps de préparation (en minutes)", "attr" => ["class" => "bg-primary", "value" => 15]])
            ->add('step', TextareaType::class, ["label" => "Etapes", "attr" => ["class" => "bg-primary", "placeholder" => "Les étapes de la recette"]])
            ->add('difficulty', ChoiceType::class, ["multiple" => false, "expanded" => false, "choices" => ['Facile' => 1, "Moyen" => 2, "Difficile" => 3], "label" => "Temps de préparation", "attr" => ["class" => "bg-primary"]])
            ->add('portions', IntegerType::class, ["label" => "Nombre de portions", "attr" => ["class" => "bg-primary", "value" => 4]])
            ->add('category', EntityType::class, ["multiple" => false, "expanded" => false, "class" => Category::class, "choice_label" => "title", "label" => "Catégorie", "attr" => ["class" => "bg-primary text-dark"]])
            ->add('allergens', EntityType::class, ["multiple" => true, "expanded" => false, "class" => Allergen::class, "choice_label" => "name", "label" => "Allergies", "attr" => ["class" => "bg-primary"]])
            ->add('diets', EntityType::class, ["multiple" => true, "expanded" => false, "class" => Diet::class, "choice_label" => "name", "label" => "Régimes", "attr" => ["class" => "bg-primary"]] )
            ->add('containsIngredients', CollectionType::class, ['entry_type' => ContainsIngredientType::class, "allow_add" => true, "allow_delete" => true, "prototype" => true, "by_reference" => false, "label" => "Ingrédients", "attr" => ["class" => "bg-primary"]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
            "attr" => ["novalidate" => "novalidate"]
        ]);
    }
}
