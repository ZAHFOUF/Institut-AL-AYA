<?php

namespace AlAya\Common\Form;

use AlAya\Common\Entity\Prestation;
use AlAya\Common\Entity\Programme;
use AlAya\Common\Entity\Agent;
use AlAya\Common\Entity\Formula;
use AlAya\Common\Repository\AgentRepository;
use AlAya\Common\Repository\FormulaRepository;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrestationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('programme', EntityType::class, [
                'class' => Programme::class,
                'choice_label' => 'name', // adapte si besoin
                'label' => 'Programme',
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mb-3'],
                'placeholder' => 'Sélectionnez un programme',
            ])
            ->add('agent', EntityType::class, [
                'class' => Agent::class,
                'choice_label' => function (Agent $agent) {
                    return $agent->getFullName(); // adapte si besoin
                },
                'query_builder' => function (AgentRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->where('a.type = :type')
                        ->setParameter('type', 2);
                },
                'label' => 'Professeur(e) référent(e)',
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mb-3'],
                'placeholder' => 'Sélectionnez un(e) Professeur(e) référent(e)	',
                'required' => false,
            ])
            ->add('formula', EntityType::class, [
                'class' => Formula::class,
                'choice_label' => function (Formula $formula) {
                    return $formula->getFullName(); // adapte si besoin
                }, // adapte si besoin
                'query_builder' => function (FormulaRepository $qb) {
                return $qb->createQueryBuilder('f')
                    ->andWhere("f.type = 1")
                    ->orderBy('f.id', 'ASC');
            },
                'label' => 'Forfait	',
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mb-3'],
                'placeholder' => 'Sélectionnez une forfait',
            ])
            ->add('student', EntityType::class, [
                'class' => \AlAya\Common\Entity\Student::class,
                'choice_label' => function ($student) {
                    return method_exists($student, 'getFullName') ? $student->getFullName() : (string)$student;
                },
                'label' => 'Étudiant(e)',
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mb-3'],
                'required' => false,
                'placeholder' => 'Sélectionnez un(e) étudiant(e)',
            ])
            ->add('rate',IntegerType::class, [
                'label' => 'Fréquence  Cours/Audio/d\'heure par Semaine',
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mb-3'],
                'required' => false,
            ])
         /*   ->add('groupe', EntityType::class, [
                'class' => \AlAya\Common\Entity\Group::class,
                'choice_label' => function ($group) {
                    return method_exists($group, 'getName') ? $group->getName() : (string)$group;
                },
                'label' => 'Groupe',
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mb-3'],
                'placeholder' => 'Sélectionnez un groupe',
            ]) */
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prestation::class,
        ]);
    }
}