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
                'label_attr' => ['class' => 'font-medium dark:text-white'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'attr' => [
                    'id' => 'productName',
                    'class' => 'rounded-xl dark:bg-dark-card dark:text-white w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix du produit',
                'label_attr' => ['class' => 'font-medium dark:text-white'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'currency' => false,
                'divisor' => 100,
                'attr' => [
                    'placeholder' => '12.39 pour 12€39',
                    'id' => 'productPrice',
                    'class' => 'rounded-xl dark:bg-dark-card dark:text-white w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
            ])
            ->add('productCategory', ChoiceType::class, [
                'choices' => [
                    'Entrée' => 'entrée',
                    'Plat' => 'plat',
                    'Dessert' => 'dessert',
                ],
                'label_attr' => ['class' => 'font-medium dark:text-white'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'attr' => [
                    'id' => 'productCategory',
                    'class' => 'rounded-xl dark:bg-dark-card dark:text-white w-96 h-10 mt-1 px-2 border border-solid border-gray-300  focus:outline-none focus:ring-2 focus:ring-gray-300 bg-white focus:border-transparent shadow-form'
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
