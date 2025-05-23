<?php

namespace AlAya\Common\Form;

use AlAya\Common\Entity\Session;
use AlAya\Common\Entity\SessionLine;
use AlAya\Common\Entity\SessionLineStatus;
use AlAya\Common\Entity\Skill;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SessionLineFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('objective',TextType::class,['required' => true , 'attr' => ['class' => 'form-control'] ,   'row_attr' => ['class' => 'form-group col-md-6 mt-2' ],])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'required' => true ,
                'attr' => ['class' => 'form-control'] ,
                'row_attr' => ['class' => 'form-group col-md-6 mt-2' ],
            ])
            ->add('hours',TextType::class,['required' => true , 'attr' => ['class' => 'form-control' , 'pattern' => '^\d+(\.\d*)?$' , ] ,   'row_attr' => ['class' => 'form-group col-md-6 mt-2'], "label" => "Nombre d'heures"])
            ->add('timeStart', null, [
                'widget' => 'single_text',
                'row_attr' => ['class' => 'form-group col-md-6 mt-3' ],
                "label" => "L'heure du début"
            ])
            ->add('timeEnd', null, [
                'widget' => 'single_text',
                'row_attr' => ['class' => 'form-group col-md-6 mt-3' ],
                "label" => "L'heure du fin"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SessionLine::class,
        ]);
    }
}
