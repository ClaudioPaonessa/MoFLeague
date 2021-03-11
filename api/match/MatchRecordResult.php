<?php

session_start();

require_once '../../auth/check_login.php';
require_once '../../helper/url_id_helper.php';
require_once '../../helper/match_helper.php';
require_once '../../db/pdo.php';

$matchId = get_id();

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$player1GamesWon = intval($request->player1GamesWon);
$player2GamesWon = intval($request->player2GamesWon);

if (!checkIfAllowed($matchId, $_SESSION["id"], $pdo)) {
    echo 'Not allowed to record this match result.';
    header("HTTP/1.1 404 Not Found");
    die();
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
    header("HTTP/1.1 404 Not Found");
    die();
}

http_response_code(200);

?>
