<?php

use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Arrangement\UKMFestival;
use UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt;


require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall(['hendelseId', 'season'], [], ['GET', 'POST'], false);
$hendelseId = $handleCall->getArgument('hendelseId');

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

$retHendelse = null;


$hendelser = $arrangement->getProgram()->getAbsoluteAll();
foreach( $hendelser as $hendelse ) {
    // var_dump($hendelse);
    if($hendelse->erSynligRammeprogram()) {
        if($hendelse->getId() == $hendelseId) {
            $retHendelse = $hendelse;
            break;
        }
    }
}


$handleCall->sendToClient([
    'hendelse' => $retHendelse,
]);