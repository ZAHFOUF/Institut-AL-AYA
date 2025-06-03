<?php

namespace AlAya\Agent\PrestationBundle\WorkFlow;

use AlAya\Agent\PrestationBundle\Service\BillGenerator;
use AlAya\Common\Entity\Prestation;
use AlAya\Common\Service\BaseWorkFlow;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Workflow\Registry;

class WorkFlowPrestation extends BaseWorkFlow
{
    

     public function __construct(
        public MailerInterface $mailer,
        public BillGenerator $billGenerator,
        public ParameterBagInterface $parameter,
        public UrlGeneratorInterface $urlGenerator,
        public Registry $workflowRegistry, 
        public EntityManagerInterface $entityManager, 
        public TokenStorageInterface $security
     ) {
          parent::__construct(
               $workflowRegistry, 
               $entityManager, 
               $security, 
               $mailer
          );      
     }
      
   
     // Brouillon
     public function enBrouillon() {
          
     }

     // En attendant le paiement
     public function onEnattendantlepaiement(Prestation $entity) {
           // Création de l'email
           $id = $entity->getId() ;
           if ($entity->getStudent() and $entity->getStudent()->getEmail()) {
               $eleve = $entity->getStudent()->getEmail();
               $bill = $this->billGenerator->generateBill($entity);
               $mail =  (new TemplatedEmail())
                ->from($this->parameter->get('from.mail'))
                ->to($eleve)
                ->subject('Votre facture ')
                ->htmlTemplate('@AgentPrestationBundle/mail.twig')
                ->attach($bill->output(), 'facture_'.'pdf', 'application/pdf')
                ->context([
                    'link' => $this->parameter->get('project.host') . $this->urlGenerator->generate('student_checkout', [
                        'prestation' => $id
                    ])
                ]);
                $this->mailer->send($mail);
           }
     }

     // En cours
     public function OnEncours() {
          
     }

     // Clôturée
     public function OnCloturée() {
          
     }

     


}
