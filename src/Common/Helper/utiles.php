<?php

use AlAya\Common\Entity\Prestation;

if (!function_exists('imageToBase64')) {

     function imageToBase64($path) {
        $path = $path;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }  

}

if (!function_exists('now')) {

    function now() {
       return new DateTimeImmutable("now");
   }  

}

if (!function_exists('getFrenchMonth')) {

    function getFrenchMonth($monthNumber) {
        $monthNumber = ltrim($monthNumber, '0');
        $months = [
            1 => 'Janvier',
            2 => 'Février',
            3 => 'Mars',
            4 => 'Avril',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juillet',
            8 => 'Août',
            9 => 'Septembre',
            10 => 'Octobre',
            11 => 'Novembre',
            12 => 'Décembre'
        ];

        return $months[$monthNumber] ?? null;
    }

    if (!function_exists("path")) {
       function path(...$args)
          {
            return implode('/', $args);
        }

    }


    function concat(...$args)
    {
        return implode('', $args);
    }

    if (!function_exists("dateFormat")) {

         function dateFormat(\DateTimeInterface|string|null $date, string $format = 'd/m/Y') {
                  if (!$date) return null;

                  if (is_string($date)) {
                  try {
                        $date = new \DateTime($date);
                   } catch (\Exception) {
                       return null;
                   }
                   }

                   return $date->format($format);
        }

    }

    if (!function_exists("numberToFrenchLetters")) {
        function numberToFrenchLetters($number) {
            $units = array('', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf');
            $teens = array('dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf');
            $tens = array('', '', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante', 'quatre-vingt', 'quatre-vingt');
            $hundreds = array('', 'cent', 'deux-cent', 'trois-cent', 'quatre-cent', 'cinq-cent', 'six-cent', 'sept-cent', 'huit-cent', 'neuf-cent');
            
            if ($number < 0 || $number > 2000) {
                return "Number out of range";
            }
        
            if ($number == 0) {
                return 'zéro';
            }
        
            $result = '';
        
            if ($number >= 1000) {
                $result .= $units[(int)($number / 1000)] . ' mille ';
                $number %= 1000;
            }
        
            if ($number >= 100) {
                $result .= $hundreds[(int)($number / 100)] . ' ';
                $number %= 100;
            }
        
            if ($number >= 20) {
                $result .= $tens[(int)($number / 10)] . ' ';            
                if ($number >= 70 and $number <= 79) {
                    $result .= $teens[(int)($number % 70  )] . ' ';
                    $number = 0 ;
                }elseif  ($number >= 90 and $number <= 99) {
                    $result .= $teens[(int)($number % 90  )] . ' ';
                    $number = 0 ;
                }
                
                else{
                    $number %= 10;
                }
               
            } elseif ($number >= 10) {
                $result .= $teens[$number - 10];
                return $result;
            }
        
            if ($number > 0) {
                $result .= $units[$number];
            }
        
            return $result;
        }
    }


    if (!function_exists("inscrasePerformance")) {

        ini_set('memory_limit', '-1');

         ini_set("max_execution_time","-1");
    }

    if (!function_exists("getEntityClass")) {
        function getEntityClass(string $name, string $namespace = 'AlAya\\Common\\Entity'): ?string
        {
            $fqcn = $namespace . '\\' . $name;  
           return class_exists($fqcn) ? $fqcn : null;
        }

    }
     

    if (!function_exists("calculerTotalPrestation")) {
        function calculerTotalPrestation(Prestation $prestation): float
        {
            $total = 0.0;
            $cours = $prestation->getSessions()->filter(function ($session) {
                return $session->isPayed() != true;
            });
            $autresPrestations = $prestation->getPrestationLines()->filter(function ($session) {
                return $session->isPayed() != true;
            });
            $totalCours = 0.0;
            foreach ($cours as $session) {
                $totalCours += $session->getHours();
            }
            $total += $totalCours * $prestation->getFormula()->getPrice();
            foreach ($autresPrestations as $prestationLine) {
                $total += $prestationLine->getQte() * $prestationLine->getFormula()->getPrice();
            }
            return $total;
        }
       

    }
    

    function calculerHeuresCours(Prestation $prestation): float
    {
        $total = 0.0;
        $cours = $prestation->getSessions()->filter(function ($session) {
            return $session->isPayed() != true;
        });
        foreach ($cours as $session) {
            $total += $session->getHours();
        }
        return $total;
        
    }

    function prixPrestation(Prestation $prestation) {
       return  $prestation->getFormula()->getPrice() ;
    }
    

    function calculerTotalHeuresCours(Prestation $prestation): float{
        $total = 0.0;
        $cours = $prestation->getSessions()->filter(function ($session) {
            return $session->isPayed() != true;
        });
        foreach ($cours as $session) {
            $total += $session->getHours();
        }
        return $total * $prestation->getFormula()->getPrice();
        
    }

}