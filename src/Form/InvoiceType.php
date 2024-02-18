<?php

namespace App\Form;

use App\Entity\Devis;
use App\Entity\Invoice;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('paymentStatus')
            ->add('taxe')
            ->add('totalPrice')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('invoiceNumber')
            ->add('totalDuePrice')
            ->add('remise')
            ->add('paymentDueTime')
            ->add('devis', EntityType::class, [
                'class' => Devis::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
