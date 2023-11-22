<?php

namespace App\Form;

use App\Entity\Devis;
use App\Entity\Customer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('taxe', null, ['label' => 'Taxe'])
            ->add('totalPrice', null, ['label' => 'Prix Total'])
            ->add('totalDuePrice', null, ['label' => 'Total Dû'])
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'choice_label' => function (Customer $customer) {
                    return $customer->getName() . ' ' . $customer->getLastName();
                },
                'label' => 'Client'
            ])
            ->add('devisProducts', CollectionType::class, [
                'entry_type' => DevisProductType::class,
                'label' => false,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Devis::class,
        ]);
    }
}
