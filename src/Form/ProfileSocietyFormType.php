<?php

namespace App\Form;

use App\Entity\Society;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ProfileSocietyFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la société',
                'label_attr' => ['class' => 'font-medium dark:text-white'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'attr' => [
                    'class' => 'rounded-xl dark:bg-dark-card dark:text-white w-full md:w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'label_attr' => ['class' => 'font-medium dark:text-white'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'attr' => [
                    'class' => 'rounded-xl dark:bg-dark-card dark:text-white w-full md:w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'label_attr' => ['class' => 'font-medium dark:text-white'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'attr' => [
                    'class' => 'rounded-xl dark:bg-dark-card dark:text-white w-full md:w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'label_attr' => ['class' => 'font-medium dark:text-white'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'attr' => [
                    'class' => 'rounded-xl dark:bg-dark-card dark:text-white w-full md:w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
            ])
            ->add('siret', TextType::class, [
                'label' => 'SIRET',
                'label_attr' => ['class' => 'font-medium dark:text-white'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'attr' => [
                    'class' => 'rounded-xl dark:bg-dark-card dark:text-white w-full md:w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
            ])
            ->add('logo', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Logo de la société',
                'label_attr' => ['class' => 'font-medium dark:text-white'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'attr' => [
                    'class' => 'rounded-xl dark:bg-dark-card dark:text-white w-full md:w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form',
                    'accept' => ".png,.jpeg,.jpg"
                ],
                'multiple' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Le fichier doit être un jpg, jpeg ou png',
                    ])
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
