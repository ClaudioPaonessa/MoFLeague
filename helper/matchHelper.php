<?php

require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';

function checkIfAllowed($matchId, $accountId) {
    $query = 'SELECT m.match_id
        FROM matches AS m
        WHERE (m.match_id = :match_id) AND ((m.player_id_1 = :player_id) OR (m.player_id_2 = :player_id))';

    $values = [':match_id' => $matchId, ':player_id' => $accountId];

    $res = executeSQL($query, $values);
    $row = $res->fetch(PDO::FETCH_ASSOC);

    if (is_array($row)) {
        return TRUE;
    }
    
    return FALSE;
}

function checkIfReporter($matchId, $accountId) {
    $query = 'SELECT mr.match_id
        FROM match_results AS mr
        WHERE (mr.match_id = :match_id) AND (mr.reporter_account_id = :player_id)';

    $values = [':match_id' => $matchId, ':player_id' => $accountId];

    $res = executeSQL($query, $values);
    $row = $res->fetch(PDO::FETCH_ASSOC);

    if (is_array($row)) {
        return TRUE;
    }
    
    return FALSE;
}

function addMatchResult($matchId, $player1GamesWon, $player2GamesWon, $reporterId) {
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

    executeSQL($query, $values);
}

function getPlayer1Id($matchId) {
    $query = 'SELECT m.player_id_1
        FROM matches AS m
        WHERE (m.match_id = :match_id)';

    $values = [':match_id' => $matchId];

    $res = executeSQL($query, $values);
    $row = $res->fetch(PDO::FETCH_ASSOC);

    if (is_array($row)) {
        extract($row);

        return $player_id_1;
    }
    
    returnError("Match not found");    
}

function getPlayer2Id($matchId) {
    $query = 'SELECT m.player_id_2
        FROM matches AS m
        WHERE (m.match_id = :match_id)';

    $values = [':match_id' => $matchId];

    $res = executeSQL($query, $values);
    $row = $res->fetch(PDO::FETCH_ASSOC);

    if (is_array($row)) {
        extract($row);

        return $player_id_2;
    }
    
    returnError("Match not found");
}

function addCardTrade($matchId, $cardId, $authorId, $player2Id) {
    $query = 'INSERT INTO card_trades (match_id, card_id, author_account_id, receiver_account_id, trade_confirmed) 
            VALUES (:match_id, :card_id, :author_account_id, :receiver_account_id, :trade_confirmed)';
    $values = [':match_id' => $matchId, ':card_id' => $cardId, ':author_account_id' => $authorId, ':receiver_account_id' => $player2Id, ':trade_confirmed' => FALSE];

    executeSQL($query, $values);
}

function revokeMatchResult($matchId) {
    $query = 'DELETE FROM match_results WHERE (match_id = :match_id)';
    $values = [':match_id' => $matchId];

    executeSQL($query, $values);
}

function revokeTrade($matchId) {
    $query = 'DELETE FROM card_trades WHERE (match_id = :match_id)';
    $values = [':match_id' => $matchId];

    executeSQL($query, $values);
}

function acceptMatchResult($matchId) {
    $query = 'UPDATE match_results SET result_confirmed = :confirmed WHERE (match_id = :match_id)';
    $values = [':match_id' => $matchId, ':confirmed' => TRUE];

    executeSQL($query, $values);
}

function acceptTrade($matchId) {
    $query = 'UPDATE card_trades SET trade_confirmed = :confirmed WHERE (match_id = :match_id)';
    $values = [':match_id' => $matchId, ':confirmed' => TRUE];

    executeSQL($query, $values);
}

?>