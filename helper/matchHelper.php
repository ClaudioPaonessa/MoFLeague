<?php

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

?>