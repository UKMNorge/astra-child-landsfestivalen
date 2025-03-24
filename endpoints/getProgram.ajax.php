<?php

use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Geografi\Kommune;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Program\Hendelser;
use UKMNorge\Arrangement\UKMFestival;

require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall([], ['visInterne'], ['GET', 'POST'], false);

$visInterne = $handleCall->getOptionalArgument('visInterne') == true ?? false;

$arrangement = UKMFestival::getCurrentUKMFestival();



$retHendelser = [];
$innslagPersoner = [];

$hendelser = $visInterne ? $arrangement->getProgram()->getAllInkludertInterne() : $arrangement->getProgram()->getAll();
foreach( $hendelser as $hendelse ) {
    if($hendelse->erSynligRammeprogram()) {
        foreach($hendelse->getInnslag()->getAll() as $innslag) {
            foreach($innslag->getPersoner()->getAll() as $person) {
                $innslagPersoner[$innslag->getId()][] = $person;
            }
        }
        $retHendelser[] = $hendelse;
    }
}


$handleCall->sendToClient([
    'hendelser' => $retHendelser,
    'innslagPersoner' => $innslagPersoner
]);