<?php

use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Arrangement\UKMFestival;
use UKMNorge\Arrangement\Aktivitet\Aktivitet;

require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall(['aktivitetId', 'season'], [], ['GET', 'POST'], false);

$aktivitetId = $handleCall->getArgument('aktivitetId');

$seasonArg = $handleCall->getArgument('season');
if (!is_numeric($seasonArg)) {
    $handleCall->sendErrorToClient('Sesong må være tall', 400);
    return;
}

$season = intval($seasonArg);

if($season == -1) {
    $arrangement = UKMFestival::getCurrentUKMFestival();
} else {
    $arrangement = UKMFestival::getBySeason($season);
}

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