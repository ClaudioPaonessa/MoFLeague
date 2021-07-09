<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/matchHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/achievementHelper.php';

$matchId = getId();

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);

if (!checkIfAllowed($matchId, $_SESSION["id"])) {
    returnError("Not authorized to record this match result.");
}

if (!isset($request->player1GamesWon) || !isset($request->player2GamesWon)) {
    returnError("Please specify a match result.");
}

$player1GamesWon = intval($request->player1GamesWon);
$player2GamesWon = intval($request->player2GamesWon);
$tradesP1toP2 = $request->tradesP1toP2;
$tradesP2toP1 = $request->tradesP2toP1;
$achievementsP1 = $request->achievementsP1;
$achievementsP2 = $request->achievementsP2;

if ((count($tradesP1toP2) > 3) || (count($tradesP2toP1) > 3)) {
    returnError("Only 3 card trades for each participant allowed.");
}

addMatchResult($matchId, $player1GamesWon, $player2GamesWon, $_SESSION["id"]);
$player1Id = getPlayer1Id($matchId);
$player2Id = getPlayer2Id($matchId);

foreach ($tradesP1toP2 as &$cardTrade) {
    $tradeInfo = get_object_vars($cardTrade);
    $cardId = $tradeInfo["id"];

    addCardTrade($matchId, $cardId, $_SESSION["id"], $player2Id);
}

foreach ($tradesP2toP1 as &$cardTrade) {
    $tradeInfo = get_object_vars($cardTrade);
    $cardId = $tradeInfo["id"];

    addCardTrade($matchId, $cardId, $_SESSION["id"], $player1Id);
}

foreach ($achievementsP1 as &$achievement) {
    $achievementInfo = get_object_vars($achievement);
    $achievementId = $achievementInfo["id"];

    addAchievement($matchId, $achievementId, $player1Id);
}

foreach ($achievementsP2 as &$achievement) {
    $achievementInfo = get_object_vars($achievement);
    $achievementId = $achievementInfo["id"];

    addAchievement($matchId, $achievementId, $player2Id);
}

http_response_code(200);

?>
