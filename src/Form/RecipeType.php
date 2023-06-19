<?php

namespace App\Form;

use App\Entity\Allergen;
use App\Entity\Category;
use App\Entity\Diet;
use App\Entity\Recipe;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ["label" => "Titre", "attr" => ["placeholder" => "Titre de la recette"]])
            ->add('description', TextType::class, ["label" => "Description", "attr" => ["placeholder" => "Courte description de la recette"]])
            ->add('picture', FileType::class, ["label" => "Illustration"])
            ->add('cookingDuration', IntegerType::class, ["label" => "Temps de cuisson", "attr" => ["placeholder" => "0"]])
            ->add('setupDuration', IntegerType::class, ["label" => "Temps de préparation", "attr" => ["placeholder" => "0"]])
            ->add('step', TextareaType::class, ["label" => "Les étapes de la recette", "attr" => ["placeholder" => "Les étapes"]])
            ->add('difficulty', ChoiceType::class, ["multiple" => false,
            "expanded" => false,
            'choices'  => [
                'Facile' => 1,
                "Moyen" => 2, 
                "Difficile" => 3,

            ]])
            ->add('portions', IntegerType::class, ["label" => "Nombre de portions", "attr" => ["placeholder" => "0"]])
            ->add('category', EntityType::class, ["multiple" => false, "expanded" => false, "class" => Category::class, "choice_label" => "title"])
            ->add('allergens', EntityType::class, ["multiple" => true, "expanded" => true, "class" => Allergen::class, "choice_label" => "name"])
            ->add('diets', EntityType::class, ["multiple" => true, "expanded" => true, "class" => Diet::class, "choice_label" => "name"] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
