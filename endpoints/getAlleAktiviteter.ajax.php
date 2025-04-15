<?php

use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Arrangement\UKMFestival;
use UKMNorge\Arrangement\Aktivitet\Aktivitet;

require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall([], [], ['GET', 'POST'], false);

$arrangement = UKMFestival::getCurrentUKMFestival();

// Aktiviteter
$aktiviteter = [];
foreach(Aktivitet::getAllByArrangement($arrangement->getId()) as $aktivitet ) {
    if(!isset($aktiviteter[$aktivitet->getId()])) {
        $aktiviteter[$aktivitet->getId()] = $aktivitet->getArrObj();
    }
}

$handleCall->sendToClient([
    'aktiviteter' => $aktiviteter,
]);