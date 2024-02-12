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
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'attr' => [
                    'id' => 'customerName',
                    'class' => 'rounded-xl dark:bg-dark-card dark:text-white w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
                'label' => 'Nom',
                'label_attr' => ['class' => 'font-medium dark:text-white'],
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
                    'class' => 'rounded-xl dark:bg-dark-card dark:text-white w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
                'label' => 'Prénom',
                'label_attr' => ['class' => 'font-medium dark:text-white'],
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
                    'class' => 'rounded-xl dark:bg-dark-card dark:text-white w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
                'label' => 'Nom de la Société',
                'label_attr' => ['class' => 'font-medium dark:text-white'],
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
                    'class' => 'rounded-xl dark:bg-dark-card dark:text-white w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
                'label' => 'Nom de la rue',
                'label_attr' => ['class' => 'font-medium dark:text-white'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'required' => false
            ])
            ->add('streetNumber', IntegerType::class, [
                'attr' => [
                    'id' => 'customerStreetNumber',
                    'class' => 'rounded-xl dark:bg-dark-card dark:text-white w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
                'label' => 'Numéro de rue',
                'label_attr' => ['class' => 'font-medium dark:text-white'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'required' => false
            ])
            ->add('city', TextType::class, [
                'attr' => [
                    'id' => 'customerCity',
                    'class' => 'rounded-xl dark:bg-dark-card dark:text-white w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
                'label' => 'Ville',
                'label_attr' => ['class' => 'font-medium dark:text-white'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'required' => false
            ])
            ->add('postalCode', TextType::class, [
                'attr' => [
                    'id' => 'customerPostalCode',
                    'class' => 'rounded-xl dark:bg-dark-card dark:text-white w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
                'label' => 'Code postal',
                'label_attr' => ['class' => 'font-medium dark:text-white'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'required' => false
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'label' => 'Adresse e-mail',
                'attr' => [
                    'id' => 'customerEmail',
                    'class' => 'rounded-xl dark:bg-dark-card dark:text-white w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
                'label_attr' => ['class' => 'font-medium dark:text-white'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'constraints' => [
                    new Email([
                        'message' => 'Veuillez renseigner une adresse e-mail valide.',
                    ]),
                    new NotBlank([
                        'message' => 'Veuillez renseigner une adresse e-mail.',
                    ])
                ],
            ])

            ->add('phoneNumber', TextType::class, [
                'required' => false,
                'label' => 'Numéro de téléphone',
                'attr' => [
                    'id' => 'customerPhoneNumber',
                    'class' => 'rounded-xl dark:bg-dark-card dark:text-white w-96 h-10 mt-1 px-2 border border-solid border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:border-transparent shadow-form'
                ],
                'label_attr' => ['class' => 'font-medium dark:text-white'],
                'row_attr' => ['class' => 'flex flex-col px-1 my-1'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner un numéro de téléphone.',
                    ]),
                    new Length([
                        'min' => 10,
                        'max' => 10,
                        'minMessage' => 'Le numéro de téléphone doit comporter 10 chiffres.',
                        'maxMessage' => 'Le numéro de téléphone doit comporter 10 chiffres.'
                    ])
                ],
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
