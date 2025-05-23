<?php


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

}