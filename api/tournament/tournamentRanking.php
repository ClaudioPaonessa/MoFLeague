<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/tournamentHelper.php';

$tournamentId = getId();

$rankingData = array();

$rankingData["tournamentName"] = getTournamentName($tournamentId);
$rankingData["ranking"] = getTournamentMatchResults($tournamentId, $_SESSION["id"]);

http_response_code(200);

echo json_encode($rankingData);

?>