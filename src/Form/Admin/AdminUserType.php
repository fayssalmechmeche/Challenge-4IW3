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

use const App\Entity\ROLE_ACOUNTANT;
use const App\Entity\ROLE_SOCIETY;

class AdminUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('roles', ChoiceType::class, [
                "multiple" => true,
                "choices" => [
                    'Entreprise' => ROLE_SOCIETY,
                    'Comptable' => ROLE_ACOUNTANT,
                ]
            ])
            ->add('society', EntityType::class, [
                'class' => Society::class,
                'choice_label' => 'name',
                'required' => true,
            ])
            ->add('name')
            ->add('lastName');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
