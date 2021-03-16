<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/matchHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../db/pdo.php';

$matchId = getId();

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$player1GamesWon = intval($request->player1GamesWon);
$player2GamesWon = intval($request->player2GamesWon);

if (!checkIfAllowed($matchId, $_SESSION["id"], $pdo)) {
    returnError("Not authorized to record this match result.");
}

addMatchResult($matchId, $player1GamesWon, $player2GamesWon, $_SESSION["id"], $pdo);

http_response_code(200);

?>
