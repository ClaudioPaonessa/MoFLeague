<?php

require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';

function addCardToPool($tournamentId, $accountId, $cardName) {
    $setId = getTournamentSetId($tournamentId);
    $cardId = getCardId($cardName, $setId);

    if ($cardId > 0) {
        $query = 'INSERT INTO initial_card_pool (tournament_id, account_id, card_id) VALUES (:tournament_id, :account_id, :card_id)';
        $values = [':tournament_id' => $tournamentId, ':account_id' => $accountId, ':card_id' => $cardId];

        executeSQL($query, $values);

        return TRUE;
    }
    else{
        return FALSE;
    }
}

function getTournamentSetId($tournamentId) {
    $query = 'SELECT t.set_id
        FROM tournaments AS t
        WHERE (t.tournament_id = :tournament_id)';

    $values = [':tournament_id' => $tournamentId];

    $res = executeSQL($query, $values);
    $row = $res->fetch(PDO::FETCH_ASSOC);

    if (is_array($row)) {
        extract($row);
        return $set_id;
    }
    
    return 0;
}

function getCardId($cardName, $setId) {
    $query = 'SELECT mc.card_id
        FROM magic_cards AS mc
        WHERE (mc.magic_set_id = :set_id) AND (mc.card_name = :card_name)';

    $values = [':set_id' => $setId, ':card_name' => $cardName];

    $res = executeSQL($query, $values);
    $row = $res->fetch(PDO::FETCH_ASSOC);

    if (is_array($row)) {
        extract($row);
        return $card_id;
    }
    
    return 0;
}

?>