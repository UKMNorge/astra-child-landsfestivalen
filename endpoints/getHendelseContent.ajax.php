<?php

use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Geografi\Kommune;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Program\Hendelser;
use UKMNorge\Arrangement\UKMFestival;
use UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt;

require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall(['hendelseId'], [], ['GET', 'POST'], false);

$hendelseId = $handleCall->getArgument('hendelseId');

$aktiviteter = [];

foreach(AktivitetTidspunkt::getAllByHendelse($hendelseId) as $tidspunkt ) {
    $aktivitet = $tidspunkt->getAktivitet();
    if(!isset($aktiviteter[$aktivitet->getId()])) {
        $aktiviteter[$aktivitet->getId()] = $aktivitet->getArrObj();
    }
}

// $hendelser = $erDeltakerProgram ? $arrangement->getProgram()->getAbsoluteAll() : $arrangement->getProgram()->getAll();
// foreach( $hendelser as $hendelse ) {
//     // var_dump($hendelse);
//     if($hendelse->erSynligRammeprogram()) {
//         foreach($hendelse->getInnslag()->getAll() as $innslag) {
//             foreach($innslag->getPersoner()->getAll() as $person) {
//                 $innslagPersoner[$innslag->getId()][] = $person;
//             }
//         }
//         $retHendelser[] = $hendelse;
//     }
// }


$handleCall->sendToClient([
    'aktiviteter' => $aktiviteter,
    // 'innslagPersoner' => $innslagPersoner
]);