<?php

namespace AlAya\Common\Repository;

use AlAya\Common\Service\QueryParser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

abstract class BaseRepository extends ServiceEntityRepository
{

    public Request $request ;
    public $connection ;
    protected array $filters = [];
    protected array $parameters = [];
    protected ?string $groupBy = null;
    protected ?string $having = null;
    protected ?string $orderBy = null;
    public $page;
    public $nbrTotal;
    public $nbrPages;
    public $doctrine;
    public $currentPages;
    public $numberPerPages;

    public function __construct(ManagerRegistry $registry,
    public TokenStorageInterface $security,
    public RequestStack $requestStack,
    public ParameterBagInterface $parameterBag,
    public Environment $twig,
    public QueryParser $queryParser,
    )
    {
        parent::__construct($registry, $this->getEntityClass());
        $this->request = $this->requestStack->getCurrentRequest() ;
        $this->connection = $this->getEntityManager()->getConnection();
    }


    public function getUser ()
    {
        return $this->security->getToken()->getUser();
    }

    public function filter(string $field, string $paramKey,$static = false): static
    {

        if ($static) {
            $paramName = str_replace('.', '_', $field);
            $this->filters[] = sprintf("%s = :%s", $field, $paramName);
            $this->parameters[$paramName] = $paramKey;
            return $this;
        }



        $value = $this->request->query->get($paramKey);
        if ($value !== null && $value !== '') {
            $paramName = str_replace('.', '_', $field);
            $this->filters[] = sprintf("%s = :%s", $field, $paramName);
            $this->parameters[$paramName] = $value;
        }
        return $this;
    }

    public function filterLike(string $field, string $paramKey,$static = false): static
    {

        if ($static) {
            $paramName = str_replace('.', '_', $field);
            $this->filters[] = sprintf("%s LIKE :%s", $field, $paramName);
            $this->parameters[$paramName] = '%' . $paramKey . '%';
            return $this;
        }

        $value = $this->request->query->get($paramKey);
        if ($value !== null && $value !== '') {
            $paramName = str_replace('.', '_', $field);
            $this->filters[] = sprintf("%s LIKE :%s", $field, $paramName,);
            $this->parameters[$paramName] = '%' . $value . '%';
        }
        return $this;
    }

    public function filterIn(string $field, string $paramKey,$static = false): static
    {

        if ($static) {
            $this->filters[] = sprintf("%s IN (%s)", $field, $paramKey);
            return $this;
        }

        $values = $this->request->query->all($paramKey);
        if (!empty($values) && is_array($values)) {
            $placeholders = [];
            foreach ($values as $index => $value) {
                $paramName = str_replace('.', '_', $field) . "_" . $index;
                $placeholders[] = ":$paramName";
                $this->parameters[$paramName] = $value;
            }
            $this->filters[] = sprintf("%s IN (%s)", $field, implode(', ', $placeholders));
        }
        return $this;
    }


    public function filterGt(string $field, string $paramKey, bool $static = false): static
{
    if ($static) {
        $paramName = str_replace('.', '_', $field);
        $this->filters[] = sprintf("%s > :%s", $field, $paramName);
        $this->parameters[$paramName] = $paramKey;
        return $this;
    }

    $value = $this->request->query->get($paramKey);
    if ($value !== null && $value !== '') {
        $paramName = str_replace('.', '_', $field);
        $this->filters[] = sprintf("%s > :%s", $field, $paramName);
        $this->parameters[$paramName] = $value;
    }

    return $this;
}

public function filterLt(string $field, string $paramKey, bool $static = false): static
{
    if ($static) {
        $paramName = str_replace('.', '_', $field);
        $this->filters[] = sprintf("%s < :%s", $field, $paramName);
        $this->parameters[$paramName] = $paramKey;
        return $this;
    }

    $value = $this->request->query->get($paramKey);
    if ($value !== null && $value !== '') {
        $paramName = str_replace('.', '_', $field);
        $this->filters[] = sprintf("%s < :%s", $field, $paramName);
        $this->parameters[$paramName] = $value;
    }

    return $this;
}

public function filterBetween(string $field, string $paramStartKey, string $paramEndKey, bool $static = false): static
{
    if ($static) {
        $paramStart = str_replace('.', '_', $field) . '_start';
        $paramEnd = str_replace('.', '_', $field) . '_end';
        $this->filters[] = sprintf("%s BETWEEN :%s AND :%s", $field, $paramStart, $paramEnd);
        $this->parameters[$paramStart] = $paramStartKey;
        $this->parameters[$paramEnd] = $paramEndKey;
        return $this;
    }

    $start = $this->request->query->get($paramStartKey);
    $end = $this->request->query->get($paramEndKey);

    if ($start !== null && $end !== null && $start !== '' && $end !== '') {
        $paramStart = str_replace('.', '_', $field) . '_start';
        $paramEnd = str_replace('.', '_', $field) . '_end';
        $this->filters[] = sprintf("%s BETWEEN :%s AND :%s", $field, $paramStart, $paramEnd);
        $this->parameters[$paramStart] = $start;
        $this->parameters[$paramEnd] = $end;
    }

    return $this;
}

public function andWhere(string $condition): static
{
    $this->filters[] = $condition;
    return $this;
}

public function addParam(string $key, mixed $value): static
{
    $this->parameters[$key] = $value;
    return $this;
}
        

    public function getSqlWhere(): string
    {
        $this->initWhere();
        return empty($this->filters) ? '' : 'WHERE ' . implode(' AND ', $this->filters);
    }

    
    public function executeQuery(string $baseSql): array
    {
        $sql = $baseSql;

        if ($where = $this->getSqlWhere()) {
            $sql .= ' ' . $where;
        }

        if ($this->groupBy) {
            $sql .= ' GROUP BY ' . $this->groupBy;
        }

        if ($this->having) {
            $sql .= ' HAVING ' . $this->having;
        }

        if ($this->orderBy) {
            $sql .= ' ' . $this->orderBy;
        }

        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->prepare($sql);
        foreach ($this->parameters as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $execute = $stmt->executeQuery();
        return  $execute->fetchAllAssociative() ;
    }

    public function paginate (string $baseSql,?int $limit = null){

        $this->numberPerPages = $limit ?? $this->parameterBag->get('pagination.limit') ;
        $this->page = $this->request->query->get('page', 1);
        $sql = $baseSql;

        if ($where = $this->getSqlWhere()) {
            $sql .= ' ' . $where;
        }

        if ($this->groupBy) {
            $sql .= ' GROUP BY ' . $this->groupBy;
        }

        if ($this->having) {
            $sql .= ' HAVING ' . $this->having;
        }

        if ($this->orderBy) {
            $sql .= ' ' . $this->orderBy;
        }

        return $this->setSQL($sql, [],$limit, $this->page);

    }


    public function initWhere()  {
        if ($this->getClassMetadata()->hasField("deleted")) {
            
        }
    }

    /**
         * Paginate with sql and return data
         *
         * @param  string  $query
         * @param  array   $params
         * @param  int     $numberPerPages
         * @param  int     $pages
         * @param  ?int    $queryCount
         * @param  ?int    $paramsCount
         * @return ?array
         */
        public function setSQL($query, $params, $numberPerPages, $pages, $queryCount = null, $paramsCount = null) : ?array
        {

            if (strpos($query, 'SELECT SQL_CALC_FOUND_ROWS') === false) {
                $query = str_replace('SELECT', 'SELECT SQL_CALC_FOUND_ROWS', $query);
            }

    
            $limit = $this->adaptationValue();
            $query .= $limit;
            $stmt = $this->connection->prepare($query);
            foreach ($this->parameters as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $execute = $stmt->executeQuery();
            $count = 0;
            $datas = $execute->fetchAll(\PDO::FETCH_ASSOC);
            unset($stmt);
    
            if(!$queryCount){
                $queryCount = 'SELECT FOUND_ROWS()';
            }
            $stmt = $this->connection->prepare($queryCount);
            if ($paramsCount) {
                foreach ($paramsCount as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
            }

            $execute = $stmt->executeQuery();
            $counts = $execute->fetchAll(\PDO::FETCH_ASSOC);
            unset($stmt);
            foreach ($counts as $data) {
                foreach ($data as $key => $value) {
                    $count = $value;
                }
            }
            $this->nbrTotal = $count;
            $this->nbrPages = ceil($count / $this->numberPerPages);
            //dd($this->nbrPages,$this->nbrTotal);
            $rangeNumber = 3;
            $start = ($this->page > $rangeNumber) ? $this->page - $rangeNumber : 1;
            $end = ($this->page + $rangeNumber > $this->nbrPages) ?
                $this->nbrPages :
                $this->page + $rangeNumber;
            $range = range($start, $end);

            $this->twig->addGlobal('pagination', [
                'currentPage' => $this->page,
                'nbPage'  => $this->nbrPages,
                'range'  => $range,
                'stringFilter' => $this->getStringFilter($this->request->query),
            ]);
            
            return $datas;
        }

        public function adaptationValue() {
         $limit = '';
         $start = (($this->page - 1) * ($this->numberPerPages));
         $end = $this->numberPerPages;
         if ($start >= 0) {
             $limit = " LIMIT " . $start . "," . $end;
         }
         $this->currentPages = $this->page;
         return $limit;
     }

     /**
         * Convert Request Object to query string for used in links
         *
         * @param Request $request
         * @param boolean $first
         * @return void
         */
        public function getStringFilter($request,$first = false) : string
        {
            $stringFilter = $first ? '?' : '&';     
            if ($request) {
            $request->remove('page');   
                foreach ($request as $keys => $datas) {
                    $nameRequest = $keys;
                    if (is_array($datas)) {
                        foreach ($datas as $key => $value) {
                            if (is_array($value)) {
                                foreach ($value as $content) {
                                    $stringFilter .= $nameRequest . '[' . $key . '][]=' . $content . '&';
                                }
                            } else {
                                $stringFilter .= $nameRequest . '[' . $key . ']=' . $value . '&';
                            }
                        }
                    } else {
                        $stringFilter .= $nameRequest . '=' . $datas . '&';
                    }
                }
                $stringFilter = substr($stringFilter, 0, strlen($stringFilter) - 1);
            }
            return $stringFilter;
        }

     public function getCurrentPages() {
        return $this->currentPages;
    }

    public function getNbrPages() {
        return $this->nbrPages;
    }

    public function getNbrTotal() {
        return $this->nbrTotal;
    }

    public function groupBy(string $group): static
    {
        $this->groupBy = $group;
        return $this;
    }

    public function having(string $condition): static
    {
        $this->having = $condition;
        return $this;
    }

    public function orderBy(string $field, string $direction = 'ASC'): static
    {
        $this->orderBy = sprintf("ORDER BY %s %s", $field, strtoupper($direction));
        return $this;
    }



    // 👇 méthode obligatoire car Doctrine ne connaît pas l'entité ici
    abstract protected function getEntityClass(): string;
    
}