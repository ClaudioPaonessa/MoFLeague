<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/matchHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../db/pdo.php';

$matchId = getId();

if (!checkIfAllowed($matchId, $_SESSION["id"], $pdo)) {
    returnError("Not allowed to accept this match result.");
}

if (checkIfReporter($matchId, $_SESSION["id"], $pdo)) {
    returnError("Not allowed to accept own reported match result.");
}

acceptMatchResult($matchId, $pdo);
acceptTrade($matchId, $pdo);

http_response_code(200);

?>
