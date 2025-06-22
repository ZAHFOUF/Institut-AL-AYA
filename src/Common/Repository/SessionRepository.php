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


    SUM(CASE f.per WHEN 'H' THEN 1 ELSE 0 END ) AS 'Nombre d\'H en direct/semaine',

    SUM(CASE f.per WHEN 'M' THEN 1 ELSE 0 END ) AS 'Nombre de cours/audios en différé/semaine',
    
    COUNT(DISTINCT p.student_id) as 'Nombre d\'élèves' ,

SUM(f.price_prof * s.hours) as 'Total Rémunération'

FROM session s 
INNER JOIN prestation p ON p.id = s.prestation_id
INNER JOIN formula f ON f.id = p.formula_id
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

    public function getSessionOfAgent($agent) 
    {
       return $this->addParam('agent', $agent->getId())
        ->executeQuery("
          SELECT s.date as date , pr.name as title , true as allDay , '' as link FROM `prestation` p
          INNER JOIN session s on s.prestation_id = p.id
          INNER JOIN programme pr ON pr.id = p.programme_id
          WHERE p.agent_id = :agent
        ");

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

    public function ca (InputBag $filter) : array{

        return $this
        ->addParam("year", $filter->get("year") ?? date("Y"))
        ->executeQuery("
        
        SELECT aff.mois AS 'Mois' , aff.year as 'Année'  , aff.direct as 'Nombre d\'H en direct /semaine' , aff.diff as 'Nombre de cours/audios en différé /semaine', aff.support as 'Supports de cours', aff.countS as 'Nombre d\'élèves' , aff.totalCours as 'Total recettes cours' , aff.supportTotal as 'Total recettes supports de cours'  , aff.sl as 'Salaire des professeurs' , aff.totalCharges as 'Autre Dépenses' , ((aff.countS + aff.totalCours) - ( aff.sl +  aff.totalCharges)) as 'Révenue total'
from (SELECT  

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
END  as mois ,
YEAR(MIN(s.date)) AS year ,


    SUM(CASE f.per WHEN 'H' THEN 1 ELSE 0 END ) AS direct ,

    SUM(CASE f.per WHEN 'M' THEN 1 ELSE 0 END ) AS diff ,
    
   (
      SELECT COUNT(pl.id)
      FROM prestation_line pl
      INNER JOIN prestation p2 ON p2.id = pl.prestation_id
      WHERE MONTH(pl.date) = MONTH(MIN(s.date))
        AND YEAR(pl.date) = YEAR(MIN(s.date))
    ) AS support,
    
    COUNT(DISTINCT p.student_id)  as countS ,
      
    SUM( CASE f.per WHEN 'H' THEN (f.price * s.hours) WHEN 'M' THEN f.price ELSE 0 END )   as totalCours ,
      
      (
      SELECT SUM(pl.qte * f2.price)
      FROM prestation_line pl
      INNER JOIN prestation p2 ON p2.id = pl.prestation_id
      INNER JOIN formula f2 on f2.id = p2.formula_id
      WHERE MONTH(pl.date) = MONTH(MIN(s.date))
        AND YEAR(pl.date) = YEAR(MIN(s.date))
    ) AS supportTotal ,

SUM(f.price_prof * s.hours) as sl ,
      
 ( SELECT SUM(c.price) FROM charge c WHERE c.per_id = 1 OR ( MONTH(MIN(s.date)) = MONTH(c.date) ) ) as totalCharges

 
      


FROM session s 
INNER JOIN prestation p ON p.id = s.prestation_id
INNER JOIN formula f ON f.id = p.formula_id
INNER JOIN agent a ON a.id = p.agent_id
WHERE s.date IS NOT NULL   AND  YEAR(s.date) = :year
GROUP BY  MONTH(s.date)
ORDER BY MONTH(s.date) DESC ) aff;


        ") ;
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
