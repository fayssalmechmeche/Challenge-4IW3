<?php

namespace App\Form\Admin;

use App\Entity\Society;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class SocietyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['placeholder' => 'Le nom de votre société. 2 caractères min.'],
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('address', TextType::class, [
                'attr' => ['placeholder' => "L'adresse de votre société. 5 caractères min."],
                'constraints' => [
                    new Length([
                        'min' => 5,
                        'minMessage' => "L'adresse doit contenir au moins {{ limit }} caractères.",
                    ]),
                ],
            ])
            ->add('phone', TelType::class, [
                'attr' => ['placeholder' => "Le téléphone de votre société. 10 chiffres min."],
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Le téléphone doit contenir au moins {{ limit }} chiffres.',
                    ]),
                ],
            ])
            ->add('email', TextType::class, [
                'attr' => ['placeholder' => "L'e-mail de votre société"],
                'constraints' => [
                    new Email(),
                ],
            ])
            ->add('siret', NumberType::class, [
                'attr' => ['placeholder' => "Le Siret de votre société"],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Society::class,
        ]);
    }
}
