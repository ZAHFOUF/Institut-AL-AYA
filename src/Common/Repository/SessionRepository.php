<?php

namespace AlAya\Common\Repository;

use AlAya\Common\Entity\Agent;
use AlAya\Common\Entity\Group;
use AlAya\Common\Entity\Prestation;
use AlAya\Common\Entity\Session;
use AlAya\Common\Service\QueryParser;
use AlAya\Common\Service\RequestGetter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\InputBag;

/**
 * @extends ServiceEntityRepository<Session>
 */
class SessionRepository extends BaseRepository
{

    public function all(InputBag $inputBag)
    {
        $query = $this->createQueryBuilder('s') ;

        if ($inputBag->get('month')) {
            $query->andWhere('MONTH(s.dateStart) = :month')
                ->setParameter('month', $inputBag->get('month'));
        }

        if ($inputBag->get('year')) {
            $query->andWhere('YEAR(s.dateStart) = :year')
                ->setParameter('year', $inputBag->get('year'));
        }

        if ($inputBag->get('class')) {
                $query->andWhere(' (SELECT gi.id from AlAya\Common\Entity\SessionGroup gi where gi.session = s.id and gi.group = ' . $inputBag->get('class') . ') > 0');
        }

        if ($this->getUser() instanceof Agent and $this->getUser()->getType()->getId() == 2) {
            $query->andWhere('s.teacher = :teacher')
                ->setParameter('teacher', $this->getUser()->getId());
        }

        return $this->queryParser->filter($query,$inputBag)
        ->orderBy("s.id","DESC")
            ->getQuery()
            ->getResult();
    }

    public function reportingIndex(InputBag $inputBag) : array {

            RequestGetter::initialize($inputBag);

            RequestGetter::isNotEmpty('month') ? RequestGetter::whereEqualSave('MONTH(s.date)','month') : null;
            RequestGetter::isNotEmpty('year') ? RequestGetter::whereEqualSave('YEAR(s.date)','year') : null;
            RequestGetter::isNotEmpty('teacher') ? RequestGetter::whereEqualSave('a.id','teacher') : null;
            $where = RequestGetter::allWhere(false);


$sql = "
SELECT  

    CONCAT(IFNULL(a.last_name,''), ' ', IFNULL(a.first_name,'')) AS 'Professeur',

    CASE MONTH(MIN(s.date))
    WHEN 1 THEN 'Janvier'
    WHEN 2 THEN 'Février'
    WHEN 3 THEN 'Mars'
    WHEN 4 THEN 'Avril'
    WHEN 5 THEN 'Mai'
    WHEN 6 THEN 'Juin'
    WHEN 7 THEN 'Juillet'
    WHEN 8 THEN 'Août'
    WHEN 9 THEN 'Septembre'
    WHEN 10 THEN 'Octobre'
    WHEN 11 THEN 'Novembre'
    WHEN 12 THEN 'Décembre'
    ELSE 'Inconnu'
END AS 'Mois',
YEAR(MIN(s.date)) AS 'Année',


    COUNT(DISTINCT s.id) AS 'Nombre cours',

SUM(s.hours) AS 'Total d\'heures réalisées' ,
/*Cours individuel : 7€/H 
○ Binôme : 6€/H 
○ Groupe : 5€/H
*/
SUM(s.hours * (CASE WHEN p.formula_id = 1 THEN 7 WHEN p.formula_id = 2 THEN 6 WHEN p.formula_id = 3 THEN 7 ELSE 0 END )) as 'Total Rémunération'

FROM session s 
INNER JOIN prestation p ON p.id = s.prestation_id
INNER JOIN agent a ON a.id = p.agent_id
WHERE s.date IS NOT NULL $where
GROUP BY a.id, MONTH(s.date)
ORDER BY MONTH(s.date) DESC ;
" ;


            $conn = $this->getEntityManager()->getConnection();
            $stmt = $conn->prepare($sql);
    
            $stmt->executeQuery();
    
    
            $execute = $stmt->executeQuery();
            return $execute->fetchAllAssociative();
    }

    protected function getEntityClass(): string
    {
        return Session::class;
    }

    public function getSessionOfAgent() 
    {
        $user = $this->getUser();
        $query = $this->createQueryBuilder("s")
                 ->where("s.teacher = :teacher")
                 ->setParameter("teacher", $user->getId());
        return $query->getQuery()->getResult();

    }

    public function getSessionOfStudent ($student){
        return $this->addParam('student', $student->getId())
        ->executeQuery("
          SELECT s.date as date , pr.name as title , true as allDay , '' as link FROM `prestation` p
          INNER JOIN session s on s.prestation_id = p.id
          INNER JOIN programme pr ON pr.id = p.programme_id
          WHERE p.student_id = :student
        ");
    }

    /**
     * Retourne les sessions pour une prestation donnée
     * @param Prestation $prestation
     * @return Session[]
     */
    public function findByPrestation(Prestation $prestation): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.prestation = :prestation')
            ->setParameter('prestation', $prestation)
            ->orderBy('s.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Session[] Returns an array of Session objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Session
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
