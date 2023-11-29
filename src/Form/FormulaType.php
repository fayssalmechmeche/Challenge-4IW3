<?php

namespace App\Form;

use App\Entity\Formula;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class FormulaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la formule'
            ])
            ->add('picture', FileType::class, [
                'label' => 'Image de la formule',
                'required' => false,
                'mapped' => false,
                'data_class' => null,
                'constraints' => [
                    new Image([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/svg+xml'
                        ],
                        'mimeTypesMessage' => 'Veuillez insérer votre photo dans un des formats autorisé (jpg, jpeg, png, svg)',
                    ]),
                ],
            ])
            ->add('selectedProduct', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name',
                'label' => 'Sélectionner un produit',
                'mapped' => false,
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
            ->add('price', IntegerType::class, [
                'label' => 'Prix de la formule',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formula::class,
        ]);
    }
}
