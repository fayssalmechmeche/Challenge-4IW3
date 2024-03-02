<?php

namespace App\Form;

use App\Entity\Devis;
use App\Entity\Invoice;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class InvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('dateValidite', DateType::class, [
                'widget' => 'single_text', // Utilise un champ de texte HTML5 pour la date, ce qui déclenche le sélecteur de date du navigateur
                // Vous pouvez ajouter d'autres options ici, comme des classes CSS pour la personnalisation
                'attr' => ['class' => 'some-custom-class'],
                'html5' => true, // S'assure que le widget utilise l'input type='date' HTML5, qui affiche le calendrier
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'Date invalide.',
                    ]),
                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
