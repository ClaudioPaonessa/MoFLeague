<?php

require_once '../../helper/errorHelper.php';

function checkIfAllowed($matchId, $accountId, $pdo) {
    $query = 'SELECT m.match_id
        FROM matches AS m
        WHERE (m.match_id = :match_id) AND ((m.player_id_1 = :player_id) OR (m.player_id_2 = :player_id))';

    $values = [':match_id' => $matchId, ':player_id' => $accountId];

    try
    {
        $res = $pdo->prepare($query);
        $res->execute($values);
    }
    catch (PDOException $e)
    {
        return FALSE;
    }

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        return TRUE;
    }

    return FALSE;
}

function checkIfReporter($matchId, $accountId, $pdo) {
    $query = 'SELECT mr.match_id
        FROM match_results AS mr
        WHERE (mr.match_id = :match_id) AND (mr.reporter_account_id = :player_id)';

    $values = [':match_id' => $matchId, ':player_id' => $accountId];

    try
    {
        $res = $pdo->prepare($query);
        $res->execute($values);
    }
    catch (PDOException $e)
    {
        return TRUE;
    }

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        return TRUE;
    }

    return FALSE;
}

function addMatchResult($matchId, $player1GamesWon, $player2GamesWon, $reporterId, $pdo) {
    /* BO3 match result validation */
    if (!($player1GamesWon === 2 && $player2GamesWon === 1) && 
    !($player1GamesWon === 1 && $player2GamesWon === 2) && 
    !($player1GamesWon === 2 && $player2GamesWon === 0) &&
    !($player1GamesWon === 0 && $player2GamesWon === 2)) {
        returnError($player1GamesWon . " : " . $player2GamesWon . " is not a valid best of three match result.");
    }

    $query = 'INSERT INTO match_results (match_id, player_1_games_won, player_2_games_won, reporter_account_id, result_confirmed) 
            VALUES (:match_id, :player_1_games_won, :player_2_games_won, :reporter_account_id, :result_confirmed)';
    $values = [':match_id' => $matchId, ':player_1_games_won' => $player1GamesWon, ':player_2_games_won' => $player2GamesWon, ':reporter_account_id' => $reporterId, ':result_confirmed' => FALSE];

    try
    {
        $res = $pdo->prepare($query);
        $res->execute($values);
    }
    catch (PDOException $e)
    {
        returnError("Error in SQL query.");
    }
}

?>