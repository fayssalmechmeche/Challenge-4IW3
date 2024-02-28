<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Society;
use App\Form\Admin\SocietyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'attr' => ['placeholder' => 'Votre e-mail'],
                'constraints' => [
                    new Email(),
                ],
            ])
            ->add('name', TextType::class, [
                'attr' => ['placeholder' => 'Votre prénom. 2 caractères min.'],
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Ce champ doit contenir au moins {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'attr' => ['placeholder' => 'Votre nom. 2 caractères min.'],
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Ce champ doit contenir au moins {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Les champs de mot de passe doivent correspondre.',
                'required' => true,
                'first_options' => ['attr' => ['placeholder' => 'Votre mot de passe. 6 caractères min.']],
                'second_options' => ['attr' => ['placeholder' => 'Confirmez votre mot de passe. 6 caractères min.']],
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('societyForm', SocietyType::class, [
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
