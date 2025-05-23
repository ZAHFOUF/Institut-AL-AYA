<?php

namespace AlAya\Common\Service ;

use AlAya\Common\Entity\Agent;
use AlAya\Common\Entity\Counter;
use AlAya\Common\Entity\Group;
use AlAya\Common\Entity\Session;
use AlAya\Common\Entity\SessionGroup;
use AlAya\Common\Entity\SessionLine;
use AlAya\Common\Entity\SessionLineStatus;
use AlAya\Common\Entity\SessionRequest;
use AlAya\Common\Entity\SessionRequestStatus;
use AlAya\Common\Entity\SessionStatus;
use AlAya\Common\Entity\SessionType;
use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class RequestAutomation
{
    public Group $group;
    public Session $session;

    public function __construct(private EntityManagerInterface $manager)
    {
        
    }

    /**
     * Auto affectation of a session request
     * 
     * @param SessionRequest $request
     */
    public function autoAffectation(SessionRequest $request) {
        $type = $request->getType()->getId();
        $function = 'automateForType' . $type;
        $this->$function($request);
    }


    public function automateForType1(SessionRequest $request) {
        try{
            $this->createGroup($request);
        }catch(\Exception $e){
            return $e->getMessage();
        }
        $request->setStatus($this->manager->getRepository(SessionRequestStatus::class)->find(2));
        $request->setClasse($this->group);
        $request->setMotif("Affectation automatique");
        $this->manager->persist($request);
        $this->manager->flush();
        $this->createSession($request,$this->group);
    }

    public function automateForType2(SessionRequest $request) {
        try{
            $this->createGroup($request);
        }catch(\Exception $e){
            return $e->getMessage();
        }
        $request->setStatus($this->manager->getRepository(SessionRequestStatus::class)->find(2));
        $request->setClasse($this->group);
        $request->setMotif("Affectation automatique");
        $this->manager->persist($request);
        $this->manager->flush();
        $this->createSession($request,$this->group);
    }

    public function automateForType3(SessionRequest $request) {

        if ($request->getGroupType()->getId() == 1) {
            try{
                $this->createGroup($request);
            }catch(\Exception $e){
                return $e->getMessage();
            }
            $request->setStatus($this->manager->getRepository(SessionRequestStatus::class)->find(2));
            $request->setClasse($this->group);
            $request->setMotif("Affectation automatique");
            $this->manager->persist($request);
            $this->manager->flush();
            $this->createSession($request,$this->group);
        }
        
    }


    /**
     * Auto validation of a session request
     * 
     * @param SessionRequest $request
     */
    public function createGroup(SessionRequest $request) {
        $group = new Group();
        /** @var Counter $counter */
        $counter = $this->manager->getRepository(Counter::class)->findOneBy(['module' => 'group']);
        $counter->setCount($counter->getCount()+1);
        $this->manager->persist($counter);
        $count = $counter->getCount();
        $group->setName($counter->getPrefix() . " " . $request->getType()->getName() . " " . $count);
        $group->setMax($this->calcTheMax($request));
        $group->setGender($request->getStudent()->getGender());
        $group->setStudents(array_merge($group->getStudents() ?? [], [$request->getStudent()->getId()]));
        $group->setBySystem(true);
        if ($group->getMax() > 1) {
            $group->setInvitationUuid(uniqid(uniqid() . "_"));
        }
        $this->manager->persist($group);
      //  $this->manager->flush();
        $this->group = $group;
    }


    public function calcTheMax(SessionRequest $request){
        $type = $request->getType() ;
        if ($type->getId() == 1) {
            return 1;
        } else if ($type->getId() == 2) {
            return 2;
        } else if ($type->getId() == 3) {
            return (int) $request->getMaxStudent();
        }
    }
    


    public function createSession(SessionRequest $request) {
        if ($request->getClasse() instanceof Group != true) {
            return 0 ;
        }
        $group = $request->getClasse();
        $session = new Session();
        $session->setDesignation($this->constributeTheSessionName($group,$request->getDateStart()));
        $session->setDateStart($request->getDateStart());
        $session->setDateEnd($this->generateEndDateOfSession($request->getDateStart()));
        $session->setType($request->getType());
        $session->setModule($request->getModule());
        $session->setAvailability($request->getAvailability());
        $session->setDays($request->getDays());
        $session->setTimezone($request->getTimezone());
        $session->setAdditionalHours($request->getAdditionalHours());
        $session->setFormula($request->getFormula());
        $session->setHours($request->getFormula()->getTotalHours() + $request->getAdditionalHours());
        $session->setStatus($this->manager->getRepository(SessionStatus::class)->find(1));
        $session->setBySystem(true);
        $this->manager->persist($session);
        $this->manager->flush();
        $this->session = $session;
        $sessionGroup = new SessionGroup();
        $sessionGroup->setGroup($group);
        $sessionGroup->setSession($session);
        $this->manager->persist($sessionGroup);
        $this->manager->flush();
        $this->createSessionLines();
    }

    // Create the planing algorithm
    public function createSessionLines () {
        $avg = $this->session->getFormula()->getAvgSession();
        $total = ($this->session->getFormula()->getTotalHours() / $this->session->getFormula()->getAvgSession()) ;
        $perWeek = ceil($total / 4);
        // Start from the next Monday
        $dates = $this->getFrenchWeekdaysDates($this->session->getDays(), $this->session->getDateStart()->format('Y-m-d'),$total,$perWeek);
        $sessionLines = [] ;

        foreach ($dates as $date) {
            $dateObject = new DateTime($date);
            $sessionLine = new SessionLine();
            $sessionLine->setSession($this->session);
            $sessionLine->setDate($dateObject);
            $sessionLine->setHours($avg);
            $sessionLine->setObjective("");
            $sessionLine->setStatus($this->manager->getRepository(SessionLineStatus::class)->find(1));
            $this->manager->persist($sessionLine);  
            $sessionLines[] = $sessionLine;
        }       

        $this->manager->flush();

        $this->findTheRightTeacher($sessionLines);
    }

    /**
     * Find the right teacher for a session request
     * 
     * @param string $aviaability
     * @param string $days
     * @param string $timezone
     * @return ?Agent
     */
    public function findTheRightTeacher($sessionLines)  {
         $aviaability = $this->session->getAvailability();
         $days = $this->session->getDays();

         // The agents already respect the availability
         $gender = $this->group->getGender()->getId();
         $module = $this->session->getModule()->getId();
         $agents = $this->manager->getRepository(Agent::class)->findAvailableAgents($aviaability,$days,$module,$gender);
         $dateStart = $sessionLines[0]->getDate()->format("Y-m-d");
         $dateEnd = $sessionLines[count($sessionLines)-1]->getDate()->format("Y-m-d");

         // Check the planning of the agents based sessionLines
         foreach ($agents as $agent) {
              $agentPlan = $this->manager->getRepository(Agent::class)->checkPlanining($agent,$dateStart,$dateEnd);
              $fullDispo = true ;
              foreach ($sessionLines as $line) {
                  $date = $this->findByDate($agentPlan,$line->getDate()->format("Y-m-d"));
                  if ($date) {
                      $total = $date["total"];
                      $hours = $line->getHours();
                      if ($total+$hours >= $agent->getMaxHours()) {
                        $fullDispo = false ;
                      }
                  }
              }

                // IF THIS AGENT IS DISPO SO HE CAN MANAGE THE GROUP
                if ($fullDispo) {

                    // WE FIND A AGENT ✅

                    // ASEIGN TO SESSION
                    $this->session->setTeacher($agent);
                    $this->manager->persist($this->session);

                    // ASAIGN TO GROUP
                    $this->group->setTeacher($agent);
                    $this->manager->persist($this->group);

                    $this->manager->flush();

                    // RETURN AND OUT 💪
                    return $agent ;
                }

             // NO AGENT FINDED 🙅‍♂️
         }

    }

    public function findByDate(array $data, string $date)
{
    foreach ($data as $item) {
        if ($item['date'] === $date) {
            return $item;
        }
    }

    return false;
}


    /**
     * Contribute the session name
     * 
     * @param Group $group
     * @param \DateTime $date
     * @return string
     */
    public function constributeTheSessionName($group,$date) {
        return "Session " . getFrenchMonth($date->format("m")) . " " . $group->getName();
    }
    
    /**
     * Generate the end date of a session
     * 
     * @param \DateTime $date
     * @return \DateTime
     */
    public function generateEndDateOfSession($date) {
        return (clone $date)->modify('+30 days');
    }

   /**
 * Get the weekdays dates in French
 * 
 * @param array $joursFr Jours en français ou ["All"]
 * @param string $dateStart Date de début
 * @param int $totalCours Nombre total de cours à générer
 * @param int|null $nbrDayPerWeek Nombre de jours max par semaine (null = illimité)
 * @return array
 */
public function getFrenchWeekdaysDates(array $joursFr, string $dateStart, int $totalCours, ?int $nbrDayPerWeek = null): array
{
    // Association des jours français aux jours anglais
    $joursMap = [
        'Lundi'    => 'Monday',
        'Mardi'    => 'Tuesday',
        'Mercredi' => 'Wednesday',
        'Jeudi'    => 'Thursday',
        'Vendredi' => 'Friday',
        'Samedi'   => 'Saturday',
        'Dimanche' => 'Sunday',
    ];

    // Si "All" est présent, on autorise tous les jours
    if (in_array('All', $joursFr)) {
        $joursEng = array_values($joursMap); // Tous les jours anglais
    } else {
        // Conversion en anglais pour comparaison
        $joursEng = array_map(fn($jourFr) => $joursMap[$jourFr] ?? null, $joursFr);
        $joursEng = array_filter($joursEng); // Supprime les null (jours invalides)
    }

    $start = new DateTime($dateStart);
    $interval = new DateInterval('P1D');

    $result = [];
    $currentWeek = null;
    $daysAddedThisWeek = 0;
    $coursCount = 0;

    // On boucle jusqu'à ce qu'on ait atteint le nombre total de cours
    while ($coursCount < $totalCours) {
        $week = $start->format('o-W');
        $dayName = $start->format('l');

        if ($week !== $currentWeek) {
            $currentWeek = $week;
            $daysAddedThisWeek = 0;
        }

        if (in_array($dayName, $joursEng)) {
            if (is_null($nbrDayPerWeek) || $daysAddedThisWeek < $nbrDayPerWeek) {
                $result[] = $start->format('Y-m-d');
                $daysAddedThisWeek++;
                $coursCount++;

                if ($coursCount >= $totalCours) {
                    break;
                }
            }
        }

        // Incrémenter d'un jour
        $start->add($interval);
    }

    return $result;
}


    
}