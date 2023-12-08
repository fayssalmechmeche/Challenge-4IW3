<?php
namespace App\Form;

use App\Entity\DevisFormula;
use App\Entity\Formula;
use App\Repository\FormulaRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DevisFormulaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        $builder
            ->add('formula', EntityType::class, [
                'class' => Formula::class,
                'query_builder' => function (FormulaRepository $fr) use ($user) {
                    return $fr->createQueryBuilder('f')
                        ->where('f.user = :user')
                        ->setParameter('user', $user);
                },
                'choice_label' => 'name',
                'label' => 'Formule'
            ])
            ->add('quantity', IntegerType::class, [
                'attr' => ['min' => 1],
                'label' => 'Quantité'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DevisFormula::class,
            'user' => null,
        ]);
    }
}
