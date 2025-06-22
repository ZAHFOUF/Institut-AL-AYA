<?php

namespace AlAya\Common\Form;

use AlAya\Common\Entity\Charge;
use AlAya\Common\Entity\ChargePer;
use AlAya\Common\Entity\Provider;
use DoctrineExtensions\Query\Mysql\Date;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChargeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('price', IntegerType::class, [
            'label' => 'Montant',
            'attr' => ['class' => 'form-control'],
            ])
            ->add('per', EntityType::class, [
            'class' => ChargePer::class, // Remplacez par le FQCN correct de l'entité
            'choice_label' => 'name', // Remplacez par le champ à afficher
            'label' => 'Type d\'abonnement',
            'attr' => ['class' => 'form-control'],
            ])
            ->add('provider', TextType::class, [
            'label' => 'Prestation',
            'attr' => ['class' => 'form-control'],
            ])
            ->add('date',DateType::class, [
            'widget' => 'single_text',
            'label' => 'Date de la charge',
            'attr' => ['class' => 'form-control' , 'display' => 'none'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Charge::class,
        ]);
    }
}
