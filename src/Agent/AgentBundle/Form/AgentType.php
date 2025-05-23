<?php

namespace AlAya\Agent\AgentBundle\Form;

use AlAya\Common\Entity\Agent;
use AlAya\Common\Entity\AgentType as EntityAgentType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AgentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('enabled' ,  CheckboxType::class , [ 'required' => false , 'attr' => [ 'class' => 'form-check-input']  , 'label' => false])
            ->add('username' , TextType::class , ['required' => true , 'attr' => [ 'class' => 'form-control'] , 'label' => false ])
            ->add('firstname' , TextType::class , ['required' => true , 'attr' => [ 'class' => 'form-control'] , 'label' => false ])
            ->add('lastname' , TextType::class , ['required' => true , 'attr' => [ 'class' => 'form-control'] , 'label' => false ])
            ->add('gender', EntityType::class, [
                'class' => \AlAya\Common\Entity\StudentGender::class,
                'choice_label' => 'name',
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'label' =>  false
            ])
            ->add('module', EntityType::class, [
                'class' => \AlAya\Common\Entity\Module::class,
                'choice_label' => 'name',
                'placeholder' => 'Touts les modules',
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'label' => false
            ])
            ->add('email' , EmailType::class , ['required' => true , 'attr' => [ 'class' => 'form-control'] , 'label' => false ])
            ->add('maxHours' , NumberType::class , ['required' => true , 'attr' => [ 'class' => 'form-control'] , 'label' => "Heures de travail par jour" ])
            ->add("price",NumberType::class, ['required' => true, 'attr' => ['class' => 'form-control'], 'label' => false])
            ->add("dispo", CheckboxType::class, ['required' => false, 'attr' => ['class' => 'form-check-input'], 'label' => false])
            ->add('type', EntityType::class, [
                'class' => EntityAgentType::class,
                'choice_label' => 'name',
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'label' => false
            ])
        ;

        if ($options['edit'] == false) {
            $builder->add('password',RepeatedType::class ,[ 'invalid_message' => 'Les champs de mot de passe doivent correspondre.', 'type' => PasswordType::class, 'required' => true , 'attr' => [ 'class' => 'form-control mb-3']  , 'first_options'  => ['label' =>  ' <label class="form-label">Mot de passe   <span class="text-danger">*</span></label>
            <button style="margin-top: 35px; margin-right: 10px;" class="btn btn-link position-absolute end-0 top-15 text-decoration-none text-muted password-addon" type="button" ><i class="ri-eye-fill align-middle"></i></button>
            '  , 'label_html' => true  ] , 'second_options'  => ['label' =>  '<label class="form-label">Confirmation de mot de passe   <span class="text-danger">*</span></label>
            <button style="margin-top: 35px; margin-right: 10px;" class="btn btn-link position-absolute end-0 top-15 text-decoration-none text-muted password-addon" type="button" ><i class="ri-eye-fill align-middle"></i></button>
            '  , 'label_html' => true  ]] );
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Agent::class,
            'edit' => false
        ]);
    }
}
