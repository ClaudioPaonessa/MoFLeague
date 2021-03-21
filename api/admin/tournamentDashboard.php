<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../auth/checkAdmin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/tournamentHelper.php';
require_once '../../db/pdo.php';

$tournamentId = getId();

$tournamentData = array();
$tournamentData["currentMatches"] = array();
$tournamentData["tournamentMatches"] = array();
$tournamentData["rounds"] = array();
$tournamentData["currentRoundId"] = -1;
$tournamentData["tournamentName"] = "";

$tournamentData["tournamentName"] = getTournamentName($tournamentId, $pdo);

$currentRoundIndex = getCurrentRoundIndex($tournamentId, $pdo);
$numberOfRounds = getNumberOfRounds($tournamentId, $pdo);
$roundsFinished = getCurrentRoundsFinished($tournamentId, $pdo);

$tournamentData["currentRoundId"] = $currentRoundIndex;
$tournamentData["numberOfRounds"] = intval($numberOfRounds);
$tournamentData["roundsFinished"] = intval($roundsFinished);

$rounds = getRounds($tournamentId, $currentRoundIndex, $pdo);
$tournamentData["rounds"] = $rounds;
$roundsKeyValuePair = getRoundsKeyValuePair($rounds);

$tournamentData["tournamentMatches"] = getTournamentMatches($tournamentId, $roundsKeyValuePair, $pdo);

if ($currentRoundIndex >= 0) {
    $tournamentData["currentMatches"] = getCurrentMatches($currentRoundIndex, $pdo);
}

http_response_code(200);

echo json_encode($tournamentData);

?>