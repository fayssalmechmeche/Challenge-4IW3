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
                'attr' => ['id' => 'customerName'],
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'groups' => ['individual'],
                        'message' => 'Veuillez renseigner le nom.',
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [
                'required' => false,
                'attr' => ['id' => 'customerLastName'],
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank([
                        'groups' => ['individual'],
                        'message' => 'Veuillez renseigner le prénom.',
                    ]),
                ],
            ])
            ->add('nameSociety', TextType::class, [
                'required' => false,
                'attr' => ['id' => 'customerNameSociety'],
                'label' => 'Nom de la Société',
                'constraints' => [
                    new NotBlank([
                        'groups' => ['society'],
                        'message' => 'Veuillez renseigner le nom de la société.',
                    ]),
                ],
            ])
            ->add('streetName', TextType::class, [
                'label' => 'Nom de la rue',
                'required' => false
            ])
            ->add('streetNumber', IntegerType::class, [
                'label' => 'Numéro de rue',
                'required' => false
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => false
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'required' => false
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'label' => 'Adresse e-mail',
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
