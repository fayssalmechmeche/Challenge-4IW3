<?php

namespace App\Form\Admin;

use App\Entity\Society;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class SocietyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class, [
                'label' => 'Nom de la société',
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'attr' => [
                    'class' => 'rounded-xl w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
            ])
            ->add('address',TextType::class, [
                'label' => 'Addresse de la société',
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'attr' => [
                    'class' => 'rounded-xl w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone de la société',
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'attr' => [

                    'class' => 'rounded-xl w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email de la société',
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'attr' => [
                    'class' => 'rounded-xl w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
            ])
            ->add('siret',  IntegerType::class, [
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
