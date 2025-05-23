<?php

namespace AlAya\Common\Form;

use AlAya\Common\Entity\Agent;
use AlAya\Common\Entity\Group;
use AlAya\Common\Entity\StudentGender;
use AlAya\Common\Repository\StudentRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class GroupForm extends AbstractType
{

    public $students ;

    public function __construct(private StudentRepository $repo) {
        $this->students = [] ;
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        array_map(fn($item)=> $this->students[$item["name"]] = $item["id"] ,$this->repo->studentsGroup($options['data']->getGender()) ) ;

        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'attr' => ['class' => 'form-control'] ,
                'row_attr' => ['class' => 'mt-2']
            ])
            ->add('max', IntegerType::class, [
                'label' => 'Capacité',
                'required' => true,
                'attr' => ['class' => 'form-control'] ,
                'row_attr' => ['class' => 'mt-2']
            ])
            ->add('gender', EntityType::class, [
                'class' => StudentGender::class,
                'choice_label' => 'name',
                'label' => 'Genre',
                'placeholder' => 'Sélectionner un genre',
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mt-2']
            ])
            ->add('teacher', EntityType::class, [
                'class' => Agent::class,
                'choice_label' => function (Agent $agent) {
                    return $agent->getFirstname() . ' ' . $agent->getLastname();
                },
                'label'=> "Professeur référent",
                'placeholder' => 'Sélectionner un professeur',
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mt-2'],
                'mapped' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er
                    ->createQueryBuilder("p")->join("p.type","t")->andWhere("t.id = 2"); }
                ]);

            if (!$options['edit']) {
                $builder->add('students', ChoiceType::class , [
                    'required' => false,
                    'label' => 'Étudiants',
                    'choices' => $this->students ,
                    'attr' => ['class' => 'form-control '  ,'data-choices' => true],
                    'mapped' => true ,
                    'multiple' => true ,
                    'row_attr' => ['class' => 'mt-2']
                ]) ;
            }
            
            $builder->add('save', SubmitType::class, [
                'label' =>  !$options['edit'] ? 'Enregistrer la classe' : 'Modifier la classe',
                'attr' => ['class' => 'btn btn-primary'] ,
                'row_attr' => ['class' => 'mt-3']
            ]);

           
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Group::class,
            'edit' => false
        ]);
    }
}
