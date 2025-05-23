<?php

namespace AlAya\Common\Form;

use AlAya\Common\Entity\SessionStudent;
use AlAya\Common\Entity\SessionStudentFiles;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SessionStudentFilesFromType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', null, [
                'label' => "Date" ,
                'widget' => 'single_text',
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mt-2'] ,
            ])
            ->add('amount',TextType::class,[
                'label' => "Montant" ,
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mt-2'] ,
            ]);

            if ($options['student'] == NULL) {
                $builder->add('sessionStudent', EntityType::class, [
                    'class' => SessionStudent::class,
                    'choice_label' => function (SessionStudent $item)  {
                        return $item->getStudent()->getLastName() . " " . $item->getStudent()->getFirstName() ;
                    },
                    'label'=> false,
                    'mapped' => true,
                    'label' => "Apprenant" ,
                    'query_builder' => function (EntityRepository $er) {
                        return $er
                        ->createQueryBuilder("p") ; } ,
                    'required' => true,
                    'attr' => ['class' => 'form-control'],
                    'row_attr' => ['class' => 'mt-2']
                ]);
            }else{
                $builder->add('sessionStudent', EntityType::class, [
                    'class' => SessionStudent::class,
                    'choice_label' => function (SessionStudent $item)  {
                        return $item->getSession()->getSession()->getDesignation()  ;
                    },
                    'label'=> false,
                    'mapped' => true,
                    'label' => "Session" ,
                    'query_builder' => function (EntityRepository $er) use ($options) {
                        return $er
                        ->createQueryBuilder("p")->distinct()->where("p.student = :student")->setParameter("student",$options['student']) ; } ,
                    'required' => true,
                    'attr' => ['class' => 'form-control'],
                    'row_attr' => ['class' => 'mt-2']
                ]);
            }
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SessionStudentFiles::class,
            'student' => NULL
        ]);
    }
}
