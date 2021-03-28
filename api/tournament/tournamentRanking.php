<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/tournamentHelper.php';
require_once '../../helper/rankingHelper.php';

$tournamentId = getId();

$rankingData = array();

$rankingData["tournamentName"] = getTournamentName($tournamentId);
$groupSize = getTournamentGroupSize($tournamentId);

$rankingData["liveRanking"] = getLiveRanking($tournamentId, $_SESSION["id"], $groupSize);
$rankingData["initialRanking"] = getInitialRanking($tournamentId, $_SESSION["id"], $groupSize);

http_response_code(200);

echo json_encode($rankingData);

?>