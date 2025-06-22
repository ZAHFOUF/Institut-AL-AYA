<?php

namespace AlAya\Common\Form;

use AlAya\Common\Entity\Purchase;
use AlAya\Common\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PurchaseFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'label' => 'Produit'
            ])
            ->add('price', NumberType::class, [
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'label' => 'Montant'
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'label' => 'Date de l\'achat'
            ])
            ->add('student', EntityType::class, [
                'class' => Student::class,
                'choice_label' => function ($student) {
                    return $student->getLastName() . ' ' . $student->getFirstName();
                },
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'label' => 'Étudiant (optionnel)'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Purchase::class,
        ]);
    }
}
