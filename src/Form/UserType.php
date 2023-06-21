<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('email', EmailType::class, ["label" => "Adresse mail", "attr" => ["class" => "bg-primary", "placeholder" => "adresse@mail.com"]])
            ->add('nickname', TextType::class, ["label" => "Pseudo", "attr" => ["class" => "bg-primary", "placeholder" => "Pseudo"]] )
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
                ], "label" => "Roles", "attr" => ["class" => "bg-primary"]
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