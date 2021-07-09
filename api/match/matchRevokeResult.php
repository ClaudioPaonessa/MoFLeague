<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/matchHelper.php';
require_once '../../helper/achievementHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../db/pdo.php';

$matchId = getId();

if (!checkIfAllowed($matchId, $_SESSION["id"])) {
    returnError("Not allowed to revoke this match result.");
}

revokeMatchResult($matchId);
revokeTrade($matchId);
removeAchievement($matchId);

http_response_code(200);

?>
