<?php

namespace AlAya\Common\Form;

use AlAya\Common\Entity\Module;
use AlAya\Common\Entity\Programme;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProgrammeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
            'label' => 'Nom',
            'attr' => ['class' => 'form-control'],
            ])
            ->add('module', EntityType::class, [
            'class' => Module::class,
            'choice_label' => 'name',
            'label' => 'Module',
            'attr' => ['class' => 'form-control'],
            ])
            ->add('hours', null, [
            'label' => 'Heures',
            'attr' => ['class' => 'form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Programme::class,
        ]);
    }
}
