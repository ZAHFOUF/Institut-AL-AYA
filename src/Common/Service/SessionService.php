<?php

namespace AlAya\Common\Service ;

use AlAya\Common\Entity\Session;
use AlAya\Common\Entity\SessionGroup;
use AlAya\Common\Entity\SessionLine;
use AlAya\Common\Entity\SessionStatus;
use AlAya\Common\Entity\Student;
use AlAya\Common\Service\Notification;
use Doctrine\ORM\EntityManagerInterface;

class SessionService 
{

    public function __construct(private Notification $notification,private EntityManagerInterface $manager,private RequestAutomation $requestAutomation)
    {
        
    }


    public function onSessionStarted (Session $session) {
        foreach ($session->getSessionGroups() as $group ) {
             $all =  $this->manager->getRepository(Student::class)->findBy(['id' => $group->getGroup()->getStudents() ]);
             foreach ($all as $s) {
                 $this->notification->sendNotificationEmail(
                     $s->getEmail(),
                     "Les séances de " . $session->getDesignation() . " ont commencé",
                     "@CommonTemplate/emails/start.twig");
             }
        }
     }


    public function onSessionLineCreated (SessionLine $sessionLine) {
           foreach ($sessionLine->getSession()->getSessionGroups() as $group ) {
                $all =  $this->manager->getRepository(Student::class)->findBy(['id' => $group->getGroup()->getStudents() ]);
                foreach ($all as $s) {
                    $this->notification->sendNotificationEmail(
                        $s->getEmail(),
                        "Séance programmé",
                        "@CommonTemplate/emails/create.twig");
                }
           }
    }

    public function cloneSession(Session $session,$date,$name) {
        $newSession = clone $session;
        $newSession->setDesignation($name);
        $newSession->setStatus($this->manager->getRepository(SessionStatus::class)->find(1));
        $newSession->setDateStart(new \DateTime($date));
        $groups = $session->getSessionGroups()->toArray();
        $newSession->getSessionGroups()->clear();
        $newSession->getSessionLines()->clear();
        $newSession->getSessionStudents()->clear();
        $this->manager->persist($newSession);
        $newGroups = [] ;
        foreach ($groups as $group) {
            $newGroup = new SessionGroup();
            $newGroup->setGroup($group->getGroup());
            $newGroup->setSession($newSession);
            $this->manager->persist($newGroup);
            $newGroups[] = $newGroup;
        }
        $this->manager->flush();
        $this->requestAutomation->session = $newSession ;
        $this->requestAutomation->group = $newGroups[0]->getGroup() ;
        $this->requestAutomation->createSessionLines();
        return $newSession; // Adjusted to return the new session instead of redirecting
    }
    
}
