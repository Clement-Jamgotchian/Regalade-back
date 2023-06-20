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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('picture')
            ->add('cookingDuration')
            ->add('setupDuration')
            ->add('step')
            ->add('difficulty')
            ->add('portions')
            ->add('category', EntityType::class, ["multiple" => false, "expanded" => false, "class" => Category::class, "choice_label" => "title"])
            ->add('allergens', EntityType::class, ["multiple" => true, "expanded" => true, "class" => Allergen::class, "choice_label" => "name"])
            ->add('diets', EntityType::class, ["multiple" => true, "expanded" => true, "class" => Diet::class, "choice_label" => "name"] )
            ->add('containsIngredients', CollectionType::class, ['entry_type' => ContainsIngredientType::class, "allow_add" => true, "allow_delete" => true, "prototype" => true, "by_reference" => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
