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

$handleCall = new HandleAPICall(['erDeltakerProgram'], [], ['GET', 'POST'], false);

$erDeltakerProgram = $handleCall->getArgument('erDeltakerProgram') == 'true' ? true : false;

$arrangement = UKMFestival::getCurrentUKMFestival();

$retHendelser = [];
$innslagPersoner = [];
$hendelseMedAktiviteter = [];


$hendelser = $erDeltakerProgram ? $arrangement->getProgram()->getAbsoluteAll() : $arrangement->getProgram()->getAll();
foreach( $hendelser as $hendelse ) {
    // var_dump($hendelse);
    if($hendelse->erSynligRammeprogram()) {
        foreach($hendelse->getInnslag()->getAll() as $innslag) {
            $innslag->getFylke();
            foreach($innslag->getPersoner()->getAll() as $person) {
                $innslagPersoner[$innslag->getId()][] = $person;
            }
        }
        $retHendelser[] = $hendelse;

        // Sjekk om det er aktiviteter tilknyttet denne hendelsen
        if(count(AktivitetTidspunkt::getAllByHendelse($hendelse->getId())) > 0) {
            $hendelseMedAktiviteter[$hendelse->getId()] = true;
        }
    }
}




$handleCall->sendToClient([
    'hendelser' => $retHendelser,
    'innslagPersoner' => $innslagPersoner,
    'hendelseMedAktiviteter' => $hendelseMedAktiviteter,
]);