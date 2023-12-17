<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Society;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'attr' => ['class' => 'custom_id rounded-xl w-full h-10 mb-1 p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent'],
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix du produit en €',
                'currency' => 'EUR',
                'divisor' => 100,
                'attr' => [
                    'placeholder' => '12.39 pour 12€39',
                    'class' => 'rounded-xl w-full h-10 mb-1 p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent',
                ],
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
            ])
            ->add('productCategory', ChoiceType::class, [
                'label' => 'Catégorie de produit',
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'attr' => ['class' => 'custom_id rounded-xl w-full h-10 mb-1 p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent'],
                'choices' => [
                    'Entrée' => 'entrée',
                    'Plat' => 'plat',
                    'Dessert' => 'dessert',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
