<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
                ->add('email', EmailType::class, [
                    "label" => "Email pour se logger"
                ])
                ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                    $builder = $event->getForm();
                    /** @var User $user */
                    $user = $event->getData();
                    if ($user->getId() !== null) {
                        $builder
                            ->add('password', PasswordType::class, [
                              "mapped" => false,
                               "label" => "le mot de passe",
                                "attr" => [
                                    "placeholder" => "laisser vide pour ne pas modifier ..."
                                ],
                            ])
                            ->add('roles', ChoiceType::class, [
                                "multiple" => true,
                                "expanded" => true,
                                "choices" => [
                                    "ADMIN" => "ROLE_ADMIN",
                                    "USER" => "ROLE_USER",
                                ]
                            ])    
                            ->add('nickname')
                            ->add('picture');
                
                    } else {
                        $builder
                        ->add('password', PasswordType::class, [
                            "mapped" => false,
                             "label" => "le mot de passe",
                              "attr" => [
                                  "placeholder" => "laisser vide pour ne pas modifier ..."
                              ],
                          ])
                            ->add('roles', ChoiceType::class, [
                                "multiple" => true,
                                "expanded" => true,
                                "choices" => [
                                    "ADMIN" => "ROLE_ADMIN",
                                    "USER" => "ROLE_USER",
                                ]
                            ])
    
                            ->add('nickname')
                            ->add('picture');
                        }
                    });
                }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
