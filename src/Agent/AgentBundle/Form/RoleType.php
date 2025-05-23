<?php

namespace AlAya\Agent\AgentBundle\Form;

use AlAya\Common\Entity\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name' ,  TextType::class , ['required' => true , 'attr' => [ 'class' => 'form-control'] , 'label' => false ])
            ->add('label' ,  TextType::class , ['required' => true , 'attr' => [ 'class' => 'form-control'] , 'label' => false ])
            ->add('description' ,  TextType::class , ['required' => true , 'attr' => [ 'class' => 'form-control'] , 'label' => false ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Role::class,
        ]);
    }
}
