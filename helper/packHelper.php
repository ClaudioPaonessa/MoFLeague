<?php

require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';

function addPack($roundId, $accountId, $packString) {
    $cards = explode(";", $packString);

    $cardIds = array();

    foreach ($cards as &$cardUrl) {
        $cardIdScryfall = explode(".", end(explode("/", $cardUrl)))[0];
        array_push($cardIds, getCardId($cardIdScryfall));
    }
    
    foreach ($cardIds as &$cardId) {
        addCard($roundId, $accountId, $cardId);
    }
}

function getCardId($cardIdScryfall) {
    $query = 'SELECT mc.card_id
            FROM magic_cards as mc
            WHERE (mc.card_id_scryfall = :card_id_scryfall)';

    $values = [':card_id_scryfall' => $cardIdScryfall];

    $res = executeSQL($query, $values);
    $row = $res->fetch(PDO::FETCH_ASSOC);

    if (is_array($row)) {
        extract($row);
        return $card_id;
    }
    
    returnError("Card with id " . $cardIdScryfall . " not found.");
}

function addCard($roundId, $accountId, $cardId) {
    $query = 'INSERT INTO pack_cards (round_id, account_id, card_id) VALUES (:round_id, :account_id, :card_id)';
    $values = [':round_id' => $roundId, ':account_id' => $accountId, ':card_id' => $cardId];

    executeSQL($query, $values);
}

?>