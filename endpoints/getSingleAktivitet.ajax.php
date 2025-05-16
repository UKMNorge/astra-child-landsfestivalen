<?php

use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Arrangement\UKMFestival;
use UKMNorge\Arrangement\Aktivitet\Aktivitet;

require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall(['aktivitetId'], [], ['GET', 'POST'], false);

$aktivitetId = $handleCall->getArgument('aktivitetId');
$arrangement = UKMFestival::getCurrentUKMFestival();

// Aktiviteter
$aktivitetReturn = null;
$tilPublikum = true;
foreach(Aktivitet::getAllByArrangement($arrangement->getId()) as $aktivitet ) {
    if($aktivitet->getId() == $aktivitetId) {
        $aktivitetReturn = $aktivitet->getArrObj($tilPublikum);
        break;
    }
}

$handleCall->sendToClient([
    'aktivitet' => $aktivitetReturn,
]);