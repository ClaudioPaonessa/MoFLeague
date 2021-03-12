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

/* BO3 match result validation */
if (!($player1GamesWon === 2 && $player2GamesWon === 1) && 
    !($player1GamesWon === 1 && $player2GamesWon === 2) && 
    !($player1GamesWon === 2 && $player2GamesWon === 0) &&
    !($player1GamesWon === 0 && $player2GamesWon === 2)) {
    returnError($player1GamesWon . " : " . $player2GamesWon . " is not a valid best of three match result.");
}

$query = 'INSERT INTO match_results (match_id, player_1_games_won, player_2_games_won, reporter_account_id, result_confirmed) 
            VALUES (:match_id, :player_1_games_won, :player_2_games_won, :reporter_account_id, :result_confirmed)';
$values = [':match_id' => $matchId, ':player_1_games_won' => $player1GamesWon, ':player_2_games_won' => $player2GamesWon, ':reporter_account_id' => $_SESSION["id"], ':result_confirmed' => FALSE];

try
{
    $res = $pdo->prepare($query);
    $res->execute($values);
}
catch (PDOException $e)
{
    returnError("Error in SQL query.");
}

http_response_code(200);

?>
