<?php

namespace App\Form\Admin;

use App\Entity\Society;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


use const App\Entity\ROLE_ACCOUNTANT;
use const App\Entity\ROLE_SOCIETY;

class AdminUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email de l\'Utilisateur',
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'attr' => [
                    'class' => 'rounded-xl w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
            ])
            ->add('roles', ChoiceType::class, [
                "multiple" => true,
                "choices" => [
                    'Entreprise' => ROLE_SOCIETY,
                    'Comptable' => ROLE_ACCOUNTANT,
                ],
                'label' => 'Role',
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'attr' => [
                    'class' => 'rounded-xl w-96 h-16 overflow-y-auto  mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 bg-white focus:border-transparent shadow-form'
                ],
            ])
            ->add('society', EntityType::class, [
                'class' => Society::class,
                'choice_label' => 'name',
                'required' => true,
                'label' => 'Société',
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'attr' => [
                    'class' => 'rounded-xl w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 bg-white focus:border-transparent shadow-form'
                ],
            ])
            ->add('name', TextType::class, [
                'label' => 'Prénom de l\'Utilisateur',
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'attr' => [
                    'class' => 'rounded-xl w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom de l\'Utilisateur',
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
            'data_class' => User::class,
        ]);
    }
}
