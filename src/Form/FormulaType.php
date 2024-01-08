<?php

namespace App\Form;

use App\Entity\Formula;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class FormulaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la formule',
                'attr' => [
                    
                    'class' => 'rounded-xl w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
            ])
            ->add('selectedProduct', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name',
                'label' => 'Sélectionner un produit',
                'attr' => [
                    
                    'class' => 'rounded-xl w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'mapped' => false,
                'query_builder' => function (ProductRepository $pr) use ($user) {
                    return $pr->createQueryBuilder('p')
                        ->where('p.user = :user')
                        ->setParameter('user', $user);
                },
                'choice_attr' => function (Product $product) {
                    return ['data-price' => $product->getPrice()];
                },
            ])

            ->add('productFormulas', CollectionType::class, [
                'entry_type' => ProductFormulaType::class,
                'entry_options' => ['label' => false],
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('productFormulasData', CollectionType::class, [
                'entry_type' => HiddenType::class,
                'allow_add' => true,
                'mapped' => false,
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix de la formule',
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'currency' => 'EUR',
                'divisor' => 100,
                'attr' => [
                    'placeholder' => '12.39 pour 12€39',
                    'id' => 'formula_price',
                    'class' => 'rounded-xl w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
            ])
            ->add('adjustPrice', CheckboxType::class, [
                'label'    => 'Ajuster le prix',
                'required' => false,
                'mapped'   => false,
                'attr'     => ['class' => 'adjust-price-checkbox rounded-xl w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'],
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formula::class,
            'user' => null,
        ]);
    }
}
