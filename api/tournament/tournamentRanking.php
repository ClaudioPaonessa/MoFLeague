<?php

session_start();

require_once '../../auth/checkLogin.php';
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

    $completedRanking = getRankingFromRounds($tournamentId, $_SESSION["id"], $previousCompletedRounds, $groupSize);

    $roundItem=array(
        "roundId" => "r" . $completedRound["roundId"],
        "untilRound" => $completedRound["name"],
        "ranking" => $completedRanking
    );

    array_push($rankingData["completedRounds"], $roundItem);
}

$rankingData["liveRoundName"] = getCurrentRound($tournamentId, $roundsKeyValuePair);

if ($rankingData["liveRoundName"] != "None") {
    if (getCurrentRoundVisible($tournamentId, $roundsVisibleKeyValuePair)) {
        $rankingData["liveRanking"] = getLiveRanking($tournamentId, $_SESSION["id"], $groupSize);
    } else {
        $rankingData["liveRanking"] = array();
    }
}

$rankingData["initialRanking"] = getInitialRanking($tournamentId, $_SESSION["id"], $groupSize);

http_response_code(200);

echo json_encode($rankingData);

?>