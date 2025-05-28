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

            RequestGetter::isNotEmpty('month') ? RequestGetter::whereEqualSave('MONTH(s.date_start)','month') : null;
            RequestGetter::isNotEmpty('year') ? RequestGetter::whereEqualSave('YEAR(s.date_start)','year') : null;
            RequestGetter::isNotEmpty('teacher') ? RequestGetter::whereEqualSave('a.id','teacher') : null;
            $where = RequestGetter::allWhere(false);


$sql = "
SELECT  

    CONCAT(IFNULL(a.last_name,''), ' ', IFNULL(a.first_name,'')) AS 'Professeur',

    -- Use ANY_VALUE() to avoid ONLY_FULL_GROUP_BY issue
    CASE MONTH(ANY_VALUE(s.date_start))
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
    YEAR(ANY_VALUE(s.date_start)) as 'Année' ,

    COUNT(DISTINCT s.id) AS 'Nombre formations',

    -- Separate subquery to ensure correct total across all sessions in the same month
    (
        SELECT COUNT(st.student_id) 
        FROM session_student st 
        INNER JOIN session_group sg ON sg.id = st.session_id 
        INNER JOIN session s3 ON sg.session_id = s3.id 
        WHERE s3.status_id > 1 
        AND MONTH(s3.date_start) = MONTH(ANY_VALUE(s.date_start)) 
        AND st.payed = 1
    ) AS 'Total élèves payés',

    SUM(s.hours) AS 'Total d\'heures prévues' ,
    SUM( (SELECT SUM(IFNULL(sl.hours,0)) from session_line sl WHERE sl.session_id = s.id) ) AS 'Total d\'heures réalisées' ,
    
    CONCAT((
        SELECT COUNT(st.student_id) 
        FROM session_student st 
        INNER JOIN session_group sg ON sg.id = st.session_id 
        INNER JOIN session s3 ON sg.session_id = s3.id 
        WHERE s3.status_id > 1 
        AND MONTH(s3.date_start) = MONTH(ANY_VALUE(s.date_start)) 
        AND st.payed = 1
    ) + a.price,' €') AS  'Prix par heure' ,
    
    CONCAT(SUM( (SELECT SUM(IFNULL(sl.hours,0)) from session_line sl WHERE sl.session_id = s.id) ) *  ((
        SELECT COUNT(st.student_id) 
        FROM session_student st 
        INNER JOIN session_group sg ON sg.id = st.session_id 
        INNER JOIN session s3 ON sg.session_id = s3.id 
        WHERE s3.status_id > 1 
        AND MONTH(s3.date_start) = MONTH(ANY_VALUE(s.date_start)) 
        AND st.payed = 1
    ) + a.price),' €') as 'Total Rémunération'

FROM session s 
INNER JOIN agent a ON a.id = s.teacher_id
WHERE s.status_id > 1 $where -- Only include valid sessions
GROUP BY a.id, MONTH(s.date_start)
ORDER BY MONTH(s.date_start) DESC ;
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

    public function getSessionOfAgent(): array
    {
        return [] ;
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
