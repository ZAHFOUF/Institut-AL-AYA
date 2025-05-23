<?php

namespace AlAya\Common\Service ;

use Symfony\Component\HttpFoundation\InputBag;

class RequestGetter 
{

    private static InputBag  $request  ;
    private static array $where ;

    public static function initialize (InputBag $inputBag) {
         self::$request = $inputBag ;
         self::$where = array();
    }

    public static function get(string|int $key)  {
        return self::$request->get($key);
    }

    public static function isEmpty(string|int $key)  {
        return empty(self::$request->get($key)) and self::$request->get($key) == "";
    }

    public static function isNotEmpty(string|int $key)  {
        return !self::isEmpty($key);
    }

    public static function clean(string|int $key)  {
        $string = self::isEmpty($key) ? $key : self::$request->get($key) ;
        return trim(str_replace("'", "\'", $string));
    }

    public static function cleanWithLike(string|int $key)  {
        return "%". self::clean($key) . "%" ;
    }

    public static function whereEqual(string $field,string|int $key)  {
        return sprintf(" $field = '%s' ",self::clean($key));
    }

    public static function whereLike(string $field,string|int $key)  {
        return sprintf(" $field LIKE '%s' ",self::cleanWithLike($key));
    }

    public static function whereEqualSave(string $field,string|int $key)  {
        self::$where[] = self::whereEqual($field,$key) ;
    }

    public static function whereLikeSave(string $field,string|int $key)  {
        self::$where[] = self::whereLike($field,$key) ;
    }
    
    
    public static function allWhere($where = true)  {
        if (count(self::$where) == 0) { return '' ; }
        $s = implode(' AND ', self::$where) ;
        return $where ? ' WHERE ' . $s   : 'AND' . $s ;
    }
}

