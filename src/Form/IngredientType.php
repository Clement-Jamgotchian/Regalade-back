<?php

namespace App\Form;

use App\Entity\Department;
use App\Entity\Ingredient;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ["label" => "Nom", "attr" => ["class" => "bg-primary", "placeholder" => "Le nom de l'ingrédient"]])
            ->add('isCold', ChoiceType::class, ["choices" => ["Frigo" => 1, "Placard" => 2], "label" => "Lieu de conservation", "attr" => ["class" => "bg-primary"]])
            ->add('unit', TextType::class, ["label" => "Unité de mesure", "attr" => ["class" => "bg-primary", "placeholder" => "L'unité de mesure de l'ingrédient"]])
            ->add('department', EntityType::class, ["multiple" => false, "expanded" => false, "class" => Department::class, "choice_label" => "name", "label" => "Rayon", "attr" => ["class" => "bg-primary"]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ingredient::class,
        ]);
    }
}
