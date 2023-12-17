<?php

    namespace App\Form;

    use App\Entity\Devis;
    use App\Entity\Customer;
    use App\Entity\Formula;
    use App\Entity\Product;
    use App\Repository\CustomerRepository;
    use Doctrine\DBAL\Types\IntegerType;
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\MoneyType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Form\Extension\Core\Type\CollectionType;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Form\Extension\Core\Type\NumberType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;

    class DevisType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $user = $options['user'];
            $builder
                ->add('devisNumber', TextType::class, [
                    'label' => false,
                    'required' => false,
                    'disabled' => true,
                    'attr' => [
                        'hidden' => true,
                    ],
                ])
                ->add('taxe', NumberType::class, [
                    'label' => 'Taxe (%)',
                    'scale' => 2,
                    'attr' => [
                        'readonly' => false,
                    ],
                    'data' => 20,
                ])
                ->add('totalPrice', MoneyType::class, [
                    'label' => 'Prix Total',
                    'attr' => [
                        'readonly' => false,
                    ],
                ])
                ->add('totalDuePrice', MoneyType::class, ['label' => 'Total TTC'])
                ->add('subject', TextareaType::class, ['label' => 'Objet', 'attr' => ['cols' => 60, // Augmentez le nombre de colonnes visibles (par exemple, 60 colonnes)
                    'rows' => 1, ]])
                ->add('customer', EntityType::class, [
                    'class' => Customer::class,
                    'query_builder' => function (CustomerRepository $cr) use ($user) {
                        return $cr->createQueryBuilder('c')
                            ->where('c.user = :user')
                            ->setParameter('user', $user);
                    },
                    'choice_label' => function (Customer $customer) {
                        if ($customer->getNameSociety() !== null) {
                            return $customer->getNameSociety();
                        } else {
                            return $customer->getName() . ' ' . $customer->getLastName();
                        }
                    },
                    'label' => false,
                    'placeholder' => 'Veuillez choisir un client',
                    'attr' => [
                        'id' => 'form_customer'  // Ajoutez cette ligne
                    ],
                ])
                ->add('devisProducts', CollectionType::class, [
                    'entry_type' => DevisProductType::class,
                    'label' => false,
                    'entry_options' => [
                        'label' => false,
                        'user' => $user,
                    ],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ])
                ->add('devisFormulas', CollectionType::class, [
                    'entry_type' => DevisFormulaType::class,
                    'label' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'entry_options' => [
                        'label' => false,
                        'user' => $user,
                    ],
                ]);

        }

        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class' => Devis::class,
                'user' => null,
            ]);
        }
    }
