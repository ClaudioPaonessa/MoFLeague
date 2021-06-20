<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../auth/checkAdmin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/tournamentHelper.php';
require_once '../../helper/rankingHelper.php';

$tournamentId = getId();
$roundId = getId();

$rankingData = array();

$rankingData["tournamentName"] = getTournamentName($tournamentId);
$groupSize = getTournamentGroupSize($tournamentId);

$rounds = getAllRounds($tournamentId);
$roundsKeyValuePair = getRoundsKeyValuePair($rounds);
$roundsVisibleKeyValuePair = getRoundsVisibleKeyValuePair($rounds);

$completedRounds = getCompletedRounds($tournamentId);

$rankingData["completedRounds"] = array();

foreach ($completedRounds as &$completedRound) {
    $previousCompletedRounds = getPreviousCompletedRoundsForRanking($tournamentId, $completedRound);

    $completedRanking = getRankingFromRounds($tournamentId, -1, $previousCompletedRounds, $groupSize);

    $roundItem=array(
        "roundId" => "r" . $completedRound["roundId"],
        "untilRound" => $completedRound["name"],
        "ranking" => $completedRanking
    );

    array_push($rankingData["completedRounds"], $roundItem);
}

$rankingData["liveRoundName"] = getCurrentRound($tournamentId, $roundsKeyValuePair);

if ($rankingData["liveRoundName"] != "None") {
    $rankingData["liveRanking"] = getLiveRanking($tournamentId, -1, $groupSize);
}

$rankingData["initialRanking"] = getInitialRanking($tournamentId, -1, $groupSize);

http_response_code(200);

echo json_encode($rankingData);

?>