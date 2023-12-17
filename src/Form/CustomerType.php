<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormError;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'attr' => [
                    'id' => 'customerName',
                    'class' => 'rounded-xl w-full h-10 mb-1 p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent'
                ],
                'label' => 'Nom',
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'constraints' => [
                    new NotBlank([
                        'groups' => ['individual'],
                        'message' => 'Veuillez renseigner le nom.',
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'required' => false,
                'attr' => [
                    'id' => 'customerLastName',
                    'class' => 'rounded-xl w-full h-10 mb-1 p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent'
                ],
                'label' => 'Prénom',
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'constraints' => [
                    new NotBlank([
                        'groups' => ['individual'],
                        'message' => 'Veuillez renseigner le prénom.',
                    ]),
                ],
            ])
            ->add('nameSociety', TextType::class, [
                'required' => false,
                'attr' => [
                    'id' => 'customerNameSociety',
                    'class' => 'rounded-xl w-full h-10 mb-1 p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent'
                ],
                'label' => 'Nom de la Société',
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'constraints' => [
                    new NotBlank([
                        'groups' => ['society'],
                        'message' => 'Veuillez renseigner le nom de la société.',
                    ]),
                ],
            ])
            ->add('streetName', TextType::class, [
                'attr' => [
                    'id' => 'customerStreetName',
                    'class' => 'rounded-xl w-full h-10 mb-1 p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent'
                ],
                'label' => 'Nom de la rue',
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'required' => false
            ])
            ->add('streetNumber', IntegerType::class, [
                'attr' => [
                    'id' => 'customerStreetNumber',
                    'class' => 'rounded-xl w-full h-10 mb-1 p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent'
                ],
                'label' => 'Numéro de rue',
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'required' => false
            ])
            ->add('city', TextType::class, [
                'attr' => [
                    'id' => 'customerCity',
                    'class' => 'rounded-xl w-full h-10 mb-1 p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent'
                ],
                'label' => 'Ville',
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'required' => false
            ])
            ->add('postalCode', TextType::class, [
                'attr' => [
                    'id' => 'customerPostalCode',
                    'class' => 'rounded-xl w-full h-10 mb-1 p-1 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent'
                ],
                'label' => 'Code postal',
                'label_attr' => ['class' => 'font-medium'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'required' => false
            ]);


        // Ajouter un écouteur d'événement pour valider les données avant la soumission du formulaire
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            // Vérifier que soit name et lastName, soit nameSociety sont remplis, mais pas les deux
            $isIndividualFilled = !empty($data['name']) && !empty($data['lastName']);
            $isSocietyFilled = !empty($data['nameSociety']);

            if (!($isIndividualFilled xor $isSocietyFilled)) {
                // Ajouter une erreur au formulaire si la condition n'est pas respectée
                $form->addError(new FormError('Vous devez remplir soit le Nom/Prénom soit le Nom de la Société'));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
