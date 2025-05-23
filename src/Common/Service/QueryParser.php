<?php

/*
 *  AutoMatically get Your params request and implement them into query (Query-Builder)
 */

namespace AlAya\Common\Service;




use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class QueryParser
{

    private $entityManager;

    // FILTERING TYPE
    public $QUERY_LIKE = array('string');
    public $QUERY_EQ = array('integer', 'boolean', 'date', 'datetime_immutable', 'datetime');
    public $QUERY_INNERJOIN = array(2);


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }



    /* 
    - Apply auto field filtering using request parameters that match entity fields. 
    - Ensure that when selecting a field, avoid using the same name as the corresponding entity.

    - params  : query ( Query Builder ) and Request params


    */



    public function filter($query ,$params )  : QueryBuilder
    {



        $metadata = $this->extractEntityAndAlias($query);
 
        $fields =  $this->getEntityFields($metadata["entity"]) ;
 
        $queryParams = $params->all();
 
     
 
 
 
        foreach ($fields as $k=> $v) {
 
 
         $regex = "/^" . $k . "(_.*)?$/"  ;
         // Find keys that match the regular expression
         $matchingKeys = array_filter($queryParams, function($key) use ($regex) {
                return preg_match($regex, $key);
         }, ARRAY_FILTER_USE_KEY);


        foreach ($matchingKeys as $key => $p) {

           // dump($p,$key);
           
            if (!is_null($p) and $p != "" ) {
    
                $p = trim($p);
    
                if (in_array( $v["type"] , $this->QUERY_EQ ) ) {
    
                    $f= $metadata["alias"] . "." . $k ;
    
                    if($v["type"] == "datetime_immutable" ){
                        $f = "date($f)" ;
                    }
                  
                    $query->andWhere(  $f . " = " . ":" . $k) ;
                    $query->setParameter($k, $p);
               
                }elseif(in_array( $v["type"] , $this->QUERY_LIKE)){
                    $query->andWhere($query->expr()->like($metadata["alias"] . "." . $k, ":" . $k));
                    $query->setParameter($k, "%$p%");
                }
                
                elseif (in_array( $v["type"] , $this->QUERY_INNERJOIN)){
                    $relation = explode("_",$key);
                    $key = $relation[0] ;
                    $target = $relation[1] ?? NULL ; 
                    $alias =  $this->generateALias($key) ;
                    $query->join($metadata["alias"] . "." . $key , $alias );
                    if (is_null($target)) {
                        $query->andWhere( $alias . ".id"  . " = " . ":" . $key);
                        $query->setParameter($key, $p);
                    }else{
                        // Temporary we using just "like" in this case 
                        $query->andWhere($query->expr()->like($alias . "." . $target, ":" . $key));
                        $query->setParameter($key, "%" .$p . "%");
                    }
                    
                    
    
                } 
        }
 
         
   
 
         }
 
           
        }
 
 
 
 
 
        return $query ;
 
         
     }

    public function getEntityFields($entityClass)
    {
        /** @var ClassMetadata $metadata */
        $metadata = $this->entityManager->getClassMetadata($entityClass);

        $fields = [];

        foreach ($metadata->getFieldNames() as $fieldName) {
            $fieldType = $metadata->getTypeOfField($fieldName);

            $fields[$fieldName] = [
                'type' => $fieldType,
                'association' => false,
            ];
        }

        foreach ($metadata->getAssociationMappings() as $fieldName => $mapping) {
            $fields[$fieldName] = [
                'type' => $mapping['type'],
                'association' => true,
            ];
        }

        return $fields;
    }


    public function extractEntityAndAlias($queryBuilder)
    {
        $dql = $queryBuilder->getDQL();


        // Extracting the alias from the DQL
        preg_match('/FROM\s+([\w\\\\]+)\s+([\w]+)/i', $dql, $matches);


        if (count($matches) >= 3) {
            $entityName = $matches[1];
            $alias = $matches[2];

            // Getting the entity class from the entity name
            $metadata = $this->entityManager->getClassMetadata($entityName);
            $entityClass = $metadata->getName();

            return [
                'entity' => $entityClass,
                'alias' => $alias,
            ];
        }

        return null;
    }


    public function generateALias($key)
    {
        // Extract the first word
        $words = explode(" ", $key);
        $firstWord = $words[0];

        // Generate a random UID of 6 letters
        $randomUid = substr(md5(uniqid()), 0, 6);

        // Concatenate the first word with the random UID
        $result = $firstWord . $randomUid;

        // return final is unique alias
        return $result;
    }
}
