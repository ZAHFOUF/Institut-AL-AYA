<?php

namespace AlAya\Agent\PrestationBundle\Form;

use AlAya\Common\Entity\Formula;
use AlAya\Common\Entity\PrestationLine;
use AlAya\Common\Repository\FormulaRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrestationLineAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('formula', EntityType::class, [
            'class' => Formula::class,
            'choice_label' => 'name',
            'placeholder' => 'Sélectionnez une prestation',
            'required' => true,
            'query_builder' => function (FormulaRepository $qb) {
                return $qb->createQueryBuilder('f')
                    ->andWhere("f.type != 1")
                    ->orderBy('f.name', 'ASC');
            },
            'label' => 'Prestation',
            'attr' => ['class' => 'form-control'],
            ])
            ->add('qte', IntegerType::class, [
                'label' => 'Quantité',
                'required' => true,
                'data' => 1,
            ])
            ->add('date', DateType::class, [
                'label' => 'Date d\'achat',
                'widget' => 'single_text',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PrestationLine::class,
        ]);
    }
}
