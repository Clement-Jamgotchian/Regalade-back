<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $user */
        $builder
            ->add('email')
            ->add('nickname')
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                $builder = $event->getForm();
                $user = $event->getData();

                if ($user->getId() === null) {
                    $builder->add('password', null, [
                        'empty_data' => '',
                        'constraints' => [
                            new NotBlank(),
                        ],
                            
                    ]);
                }
            })
            ->add('roles', ChoiceType::class, [
                "multiple" => true,
                "expanded" => true,
                "choices" => [
                    "ADMIN" => "ROLE_ADMIN",
                    "USER" => "ROLE_USER",
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}