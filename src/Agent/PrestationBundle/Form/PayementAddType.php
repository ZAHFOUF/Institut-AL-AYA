<?php

namespace AlAya\Agent\PrestationBundle\Form;

use AlAya\Common\Entity\Payement;
use AlAya\Common\Entity\PayementType;
use AlAya\Common\Entity\Prestation;
use AlAya\Common\Repository\PrestationRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PayementAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', MoneyType::class, [
                'label' => 'Montant',
                'currency' => 'EUR',
                'required' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('date', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'required' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('type', EntityType::class, [
                'class' => PayementType::class,
                'choice_label' => 'name',
                'label' => 'Méthode',
                'required' => true,
                'placeholder' => 'Sélectionnez une méthode de paiement',
                'attr' => ['class' => 'form-control']
            ]);

        if (isset($options['prestation']) && $options['prestation']) {
            $builder->add('prestation', EntityType::class, [
                'class' => Prestation::class,
                'choice_label' => function (Prestation $prestation) {
                    return  $prestation->getClient()?->getFullName() . ' - ' . $prestation->getFormula()?->getName() ;
                },
                'placeholder' => 'Sélectionnez une prestation',
                'query_builder' => function (PrestationRepository $repo) {
                    return $repo->createQueryBuilder('p');
                },
                'label' => 'Prestation',
                'required' => true,
                'attr' => ['class' => 'form-control']
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Payement::class,
            'prestation' => false
        ]);
    }
}
