<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/tournamentHelper.php';
require_once '../../helper/achievementHelper.php';

$tournamentId = getId();

$tournamentData = array();
$tournamentData["currentMatches"] = array();
$tournamentData["rounds"] = array();
$tournamentData["currentRoundId"] = -1;
$tournamentData["tournamentName"] = "";

$tournamentData["tournamentName"] = getTournamentName($tournamentId);

$currentRoundIndex = getCurrentRoundIndex($tournamentId);
$numberOfRounds = getNumberOfRounds($tournamentId);
$roundsFinished = getCurrentRoundsFinished($tournamentId);

$tournamentData["currentRoundId"] = $currentRoundIndex;
$tournamentData["numberOfRounds"] = intval($numberOfRounds);
$tournamentData["roundsFinished"] = intval($roundsFinished);

$rounds = getRounds($tournamentId, $currentRoundIndex);
$tournamentData["rounds"] = $rounds;
$roundsKeyValuePair = getRoundsKeyValuePair($rounds);

$tournamentData["tournamentMatches"] = getMatchesFiltered($tournamentId, $_SESSION["id"], $roundsKeyValuePair);

if ($currentRoundIndex >= 0) {
    $tournamentData["currentMatches"] = getCurrentMatchesFiltered($currentRoundIndex, $_SESSION["id"]);
}

$tournamentData["receivedAchievements"] = getReceivedAchievements($tournamentId, $_SESSION["id"]);
$tournamentData["receivedAchievementsPoint"] = getReceivedAchievementsPoints($tournamentId, $_SESSION["id"]);

echo json_encode($tournamentData);

http_response_code(200);

?>