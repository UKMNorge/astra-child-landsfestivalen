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


$hendelser = $visInterne ? $arrangement->getProgram()->getAllInkludertInterne() : $arrangement->getProgram()->getAll();
$program = Hendelser::sorterPerDag( $hendelser );

$hendelserDato = [];
foreach ($program as $p) {
    foreach($p->forestillinger as $f) {
        $hendelserDato[$f->getStart()->getTimestamp()][] = $f;
    }
}

$handleCall->sendToClient([
    'visInterne' => $visInterne,
    'program' => $program,
    'hendelserDato' => $hendelserDato
]);