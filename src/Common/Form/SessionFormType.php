<?php

namespace AlAya\Common\Form;

use AlAya\Common\Entity\Agent;
use AlAya\Common\Entity\Formula;
use AlAya\Common\Entity\Module;
use AlAya\Common\Entity\Session;
use AlAya\Common\Entity\SessionStatus;
use AlAya\Common\Entity\SessionType;
use AlAya\Common\Entity\Student;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SessionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('designation',TextType::class, [
                'label' => false,
                'required' => true
            ])
        //    ->add('hours')
          
          /*  ->add('additionalHours', NumberType::class, [
                'label' => false,
                'required' => false
            ]) */
          /*  ->add('availability')
            ->add('days')
            ->add('timezone') */
            ->add('type', EntityType::class, [
                'class' => SessionType::class,
                'choice_label' => 'name',
                'label'=> false,
                'mapped' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er
                    ->createQueryBuilder("p") ; }
            ])
            ->add('teacher', EntityType::class, [
                'class' => Agent::class,
                'choice_label' => function (Agent $agent) {
                    return $agent->getFirstname() . ' ' . $agent->getLastname();
                },
                'label'=> false,
                'mapped' => true,
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er
                    ->createQueryBuilder("p")->join("p.type","t")->andWhere("t.id = 2"); }
            ])
            ->add('module', EntityType::class, [
                'class' => Module::class,
                'choice_label' => 'name',
                'label'=> false,
                'mapped' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er
                    ->createQueryBuilder("p") ; }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
