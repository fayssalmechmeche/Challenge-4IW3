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
use Symfony\Component\Form\Extension\Core\Type\TextType;


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
                'attr' => ['hidden' => true],
                'choice_label' => 'name',
                'label' => false,
            ])
            ->add('quantity', IntegerType::class, [
                'attr' => ['min' => 1,'hidden' => true],
                'label' => false
            ])
        ->add('price', TextType::class, [
                'attr' => ['hidden' => true],
                'label' => false
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
