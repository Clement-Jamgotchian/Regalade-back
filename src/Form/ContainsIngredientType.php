<?php

namespace App\Form;

use App\Entity\ContainsIngredient;
use App\Entity\Ingredient;
use App\Entity\Recipe;
use Doctrine\ORM\EntityRepository;
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
            ->add('quantity', IntegerType::class, ["label" => false, "attr" => ["class" => "bg-primary", "value" => 5]])
            ->add('ingredient', EntityType::class, ["multiple" => false, "expanded" => false, "class" => Ingredient::class, "choice_label" => function ($entity)
            {
                /** @var Ingredient $entity */
                return $entity->getName() . " - (" . $entity->getUnit() . ")";
            }, "label" => false, "attr" => ["class" => "bg-primary"], "query_builder" => function(EntityRepository $entityrepository){
                return $entityrepository->createQueryBuilder('i')
                    ->orderBy('i.name', 'ASC');}])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContainsIngredient::class,
            "attr" => ["novalidate" => "novalidate"]
        ]);
    }
}
