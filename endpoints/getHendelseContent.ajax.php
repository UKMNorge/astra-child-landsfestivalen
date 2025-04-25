<?php

use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Geografi\Kommune;
use UKMNorge\Arrangement\Arrangement;
use UKMNorge\Arrangement\Program\Hendelser;
use UKMNorge\Arrangement\UKMFestival;
use UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt;
use UKMNorge\Arrangement\Program\Hendelse;

require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall(['hendelseId'], [], ['GET', 'POST'], false);

$hendelseId = $handleCall->getArgument('hendelseId');
$hendelse = new Hendelse($hendelseId);

$arrangement = UKMFestival::getCurrentUKMFestival();

// Aktiviteter
$aktiviteter = [];
foreach(AktivitetTidspunkt::getAllByHendelse($hendelse->getId()) as $tidspunkt ) {
    $aktivitet = $tidspunkt->getAktivitet();
    if(!isset($aktiviteter[$aktivitet->getId()])) {
        $aktiviteter[$aktivitet->getId()] = $aktivitet->getArrObj();
    }
}

// Innslag
$innslagPersoner = [];
$playback = [];
$hendelser = $arrangement->getProgram()->getAbsoluteAll();
foreach( $hendelser as $hendelse ) {
    if($hendelse->getId() != $hendelseId) {
        continue;
    }

    if($hendelse->erSynligRammeprogram()) {
        foreach($hendelse->getInnslag()->getAll() as $innslag) {
            if(!isset($innslagPersoner[$innslag->getId()])) {
                $innslagPersoner[$innslag->getId()] = [];
            }

            // Bilder
            $innslagPersoner[$innslag->getId()]['bilder'] = [];
            if(count($innslag->getBilder()->getAll()) > 0) {
                $innslagPersoner[$innslag->getId()]['bilder'] = $innslag->getBilder()->getAll();
            }

            // Personer
            $innslagPersoner[$innslag->getId()]['innslag'] = $innslag;
            $innslag->getPlayback()->getAll();
            foreach($innslag->getPersoner()->getAll() as $person) {
                $innslagPersoner[$innslag->getId()]['personer'][] = $person;
            }
        }
    }
}



$handleCall->sendToClient([
    'aktiviteter' => $aktiviteter,
    'innslagPersoner' => $innslagPersoner,
]);