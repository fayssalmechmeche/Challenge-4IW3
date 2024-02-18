<?php

namespace App\Form\Admin;

use App\Entity\Society;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;


class SocietyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la société',
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'attr' => [
                    'class' => 'rounded-xl w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('address', TextType::class, [
                'label' => 'Addresse de la société',
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'attr' => [
                    'class' => 'rounded-xl w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
                'constraints' => [
                    new Length([
                        'min' => 5,
                        'minMessage' => "L'adresse doit contenir au moins {{ limit }} caractères.",
                    ]),
                ],
            ])
            ->add('phone', IntegerType::class, [
                'label' => 'Téléphone de la société',
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'attr' => [

                    'class' => 'rounded-xl w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
                'constraints' => [
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Le téléphone doit contenir au moins {{ limit }} chiffres.',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email de la société',
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'attr' => [
                    'class' => 'rounded-xl w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
                'constraints' => [
                    new Email(),
                ],
            ])
            ->add('siret', IntegerType::class, [
                'label' => 'Siret de la société',
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'attr' => [
                    'class' => 'rounded-xl w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Society::class,
        ]);
    }
}
