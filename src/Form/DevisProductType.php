<?php

namespace App\Form;

use App\Entity\DevisProduct;
use App\Entity\Society;
use App\Repository\ProductRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DevisProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $society = $options['society'];
        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name',
                'label' => false,
                'attr' => ['hidden' => true],
                'choice_attr' => function($product, $key, $value) {
                    return ['data-price' => 'test'];
                },
                'query_builder' => function (ProductRepository $pr) use ($society) {
                    return $pr->createQueryBuilder('p')
                        ->where('p.society = :society')
                        ->setParameter('society', $society);
                },

            ])
            ->add('quantity', IntegerType::class, [

                'label' => false,
                'attr' => ['hidden' => true, 'min' => 0],
            ])
            ->add('price', TextType::class, [
                'attr' => ['hidden' => true],
                'label' => false
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DevisProduct::class,
            'society' => null,
        ]);
    }
}