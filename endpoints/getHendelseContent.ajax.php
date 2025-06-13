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
            $objInnslag = null;
            
            // Hent fylke
            $innslag->getFylke();

            // Hent alle filmene
            foreach($innslag->getFilmer()->getAll() as $film) {
                $filmObj = [];
                $filmObj['id'] = $film->getId();
                $filmObj['tittel'] = $film->getTitle();
                $filmObj['beskrivelse'] = $film->getDescription();
                $filmObj['embededHTML'] = $film->getEmbedHtml();    
                $objInnslag['filmer'][$film->getId()] = $filmObj;
            }

            // Bilder
            $objInnslag['bilder'] = [];
            if(count($innslag->getBilder()->getAll()) > 0) {
                $objInnslag['bilder'] = $innslag->getBilder()->getAll();
            }

            // Personer
            $objInnslag['innslag'] = $innslag;
            $innslag->getPlayback()->getAll();
            foreach($innslag->getPersoner()->getAll() as $person) {
                $personObj = [];
                $personObj['id'] = $person->getId();
                $personObj['fornavn'] = $person->getFornavn();
                $personObj['etternavn'] = $person->getEtternavn();
                $personObj['navn'] = $person->getNavn();
                $personObj['rolle'] = $person->getRolle();
                $personObj['kommune'] = $person->getKommune()->getNavn() ?? '';
                $personObj['context'] = $innslag->getContext();

                $objInnslag['personer'][] = $personObj;
            }

            $innslagPersoner[] = $objInnslag;
        }
    }
}



$handleCall->sendToClient([
    'aktiviteter' => $aktiviteter,
    'innslagPersoner' => $innslagPersoner,
    'filmer' => $filmer,
]);