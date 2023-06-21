<?php

namespace App\Form;

use App\Entity\ContainsIngredient;
use App\Entity\Ingredient;
use App\Entity\Recipe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContainsIngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', IntegerType::class, ["label" => "Quantité", "attr" => ["class" => "bg-primary"]])
            // ->add('recipe', EntityType::class, ["multiple" => false, "expanded" => false, "class" => Recipe::class, "choice_label" => "title"])
            ->add('ingredient', EntityType::class, ["multiple" => false, "expanded" => false, "class" => Ingredient::class, "choice_label" => "name", "label" => "Ingrédient", "attr" => ["class" => "bg-primary"]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContainsIngredient::class,
        ]);
    }
}
