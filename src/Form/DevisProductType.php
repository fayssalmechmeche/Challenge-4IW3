<?php

namespace App\Form;

use App\Entity\DevisProduct;
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
        $user = $options['user'];
        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name',
                'label' => 'Produit',
                'query_builder' => function (ProductRepository $pr) use ($user) {
                    return $pr->createQueryBuilder('p')
                        ->where('p.user = :user')
                        ->setParameter('user', $user);
                },
            ])
            ->add('quantity', IntegerType::class, [
                'attr' => ['min' => 0],
                'label' => 'Quantité'
            ])
            ->add('displayedPrice', TextType::class, [
                'mapped' => false, // ce champ n'est pas mappé à l'entité
                'disabled' => true, // désactivé pour l'édition
                'label' => 'Prix affiché'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DevisProduct::class,
            'user' => null,
        ]);
    }
}