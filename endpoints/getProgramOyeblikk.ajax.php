<?php

use UKMNorge\OAuth2\HandleAPICall;
use UKMNorge\Arrangement\UKMFestival;
use UKMNorge\Arrangement\Aktivitet\AktivitetTidspunkt;
use UKMNorge\Arrangement\Program\Hendelse;
use UKMNorge\Arrangement\Program\HendelseGruppe;

require_once('UKM/Autoloader.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$handleCall = new HandleAPICall(['erDeltakerProgram'], [], ['GET', 'POST'], false);

$erDeltakerProgram = $handleCall->getArgument('erDeltakerProgram') == 'true' ? true : false;

$arrangement = UKMFestival::getCurrentUKMFestival();

$retHendelser = [];
$innslagPersoner = [];
$innslagTitler = [];
$hendelseMedAktiviteter = [];
$direktesendinger = [];


$hendelser = $erDeltakerProgram ? $arrangement->getProgram()->getAbsoluteAll() : $arrangement->getProgram()->getAll();
foreach( $hendelser as $hendelse ) {
    // Legg til direksending for denne hendelsen
    $direktesendinger[$hendelse->getId()] = $hendelse->harSending() ? $hendelse->getSending() : null;

    if($hendelse->erSynligRammeprogram()) {
        foreach($hendelse->getInnslag()->getAll() as $innslag) {
            $innslag->getFylke();
            foreach($innslag->getPersoner()->getAll() as $person) {
                $personObj = [];
                $personObj['id'] = $person->getId();
                $personObj['fornavn'] = $person->getFornavn();
                $personObj['etternavn'] = $person->getEtternavn();
                $personObj['rolle'] = $person->getRolle() ? $person->getRolle() : '';
                $personObj['navn'] = $person->getNavn();
                $personObj['kommune'] = $person->getKommune()->getNavn() ?? '';
                $personObj['context'] = $innslag->getContext();

                $innslagPersoner[$innslag->getId()][] = $personObj;
            }
            
            // Add titles for this innslag if it has titles
            if($innslag->getType()->harTitler()) {
                foreach($innslag->getTitler()->getAll() as $tittel) {
                    $tittelObj = [];
                    $tittelObj['id'] = $tittel->getId();
                    $tittelObj['tittel'] = $tittel->getTittel();
                    $tittelObj['varighet'] = $tittel->getVarighet()->getHumanShort();
                    
                    // Add type-specific metadata based on innslag type
                    $innslagTypeKey = $innslag->getType()->getKey();
                    
                    if ($innslagTypeKey == "musikk") {
                        // Handle music-specific metadata
                        $tekstAv = $tittel->getTekstAv() ?? '';
                        $melodiAv = $tittel->getMelodiAv() ?? '';
                        
                        if ($tekstAv == $melodiAv && !empty($tekstAv)) {
                            $tittelObj['tekstOgMelodi'] = $tekstAv;
                        } else {
                            if (!$tittel->erInstrumental() && !empty($tekstAv)) {
                                $tittelObj['tekstAv'] = $tekstAv;
                            }
                            if (!empty($melodiAv)) {
                                $tittelObj['melodiAv'] = $melodiAv;
                            }
                        }
                    } elseif ($innslagTypeKey == "dans") {
                        // Handle dance-specific metadata
                        $koreografiAv = $tittel->getKoreografiAv() ?? '';
                        if (!empty($koreografiAv)) {
                            $tittelObj['koreografiAv'] = $koreografiAv;
                        }
                    } elseif ($innslagTypeKey == "litteratur" && !$tittel->erSelvlaget()) {
                        // Handle literature-specific metadata
                        $tekstAv = $tittel->getTekstAv() ?? '';
                        if (!empty($tekstAv)) {
                            $tittelObj['skrevet_av'] = $tekstAv;
                        }
                    }
                    
                    // // Handle exhibition-specific metadata
                    // if ($innslagTypeKey == "utstilling") {
                    //     $teknikk = $tittel->getTeknikk() ?? '';
                    //     $format = $tittel->getFormat() ?? '';
                        
                    //     if (!empty($teknikk)) {
                    //         $tittelObj['teknikk'] = $teknikk;
                    //     }
                    //     if (!empty($format)) {
                    //         $tittelObj['format'] = $format;
                    //     }
                    // }
                    
                    $innslagTitler[$innslag->getId()][] = $tittelObj;
                }
            }
        }
        
        // Create array representation of the hendelse
        $hendelseData = $hendelse;
        $hendelseData->tidbruk = $hendelse->getTid();
        $retHendelser[] = $hendelseData;

        // Sjekk om det er aktiviteter tilknyttet denne hendelsen
        if(count(AktivitetTidspunkt::getAllByHendelse($hendelse->getId())) > 0) {
            $hendelseMedAktiviteter[$hendelse->getId()] = true;
        }
    }
}

$hendelseGrupper = [];
foreach(HendelseGruppe::getAlleByArrangement($arrangement) as $hendelseGruppe) {
    $hendelseGrupper[$hendelseGruppe->getId()] = [
        'id' => $hendelseGruppe->getId(),
        'navn' => $hendelseGruppe->getNavn(),
        'beskrivelse' => $hendelseGruppe->getBeskrivelse(),
        'start' => $hendelseGruppe->getStart() ? $hendelseGruppe->getStart()->getTimestamp() : null,
        'tag' => $hendelseGruppe->getTag() ?? null,
        'hendelser' => [],
    ];
    foreach($hendelseGruppe->getHendelser() as $hendelse) {
        $hendelseGrupper[$hendelseGruppe->getId()]['hendelser'][] = $hendelse->getId();
    }
}


$handleCall->sendToClient([
    'hendelser' => $retHendelser,
    'innslagPersoner' => $innslagPersoner,
    'innslagTitler' => $innslagTitler,
    'hendelseMedAktiviteter' => $hendelseMedAktiviteter,
    'hendelseGrupper' => $hendelseGrupper,
    'direktesendinger' => $direktesendinger,
]);