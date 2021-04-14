<?php

require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';
require_once '../../helper/magicCardHelper.php';

function addPack($roundId, $accountId, $packString) {
    $cards = explode(";", $packString);

    $cardIds = array();

    foreach ($cards as &$cardUrl) {
        $cardIdScryfall = explode(".", end(explode("/", $cardUrl)))[0];
        array_push($cardIds, getCardIdScryfall($cardIdScryfall));
    }
    
    foreach ($cardIds as &$cardId) {
        addCard($roundId, $accountId, $cardId);
    }
}

function getCardIdScryfall($cardIdScryfall) {
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

function getRoundPacks($roundId) {
    $participants = getTournamentParticipants($roundId);

    foreach ($participants as &$particpant) {
        $particpant['packCards'] = getCardsInPack($roundId, $particpant["accountId"]);
    }

    return $participants;
}

function getTournamentParticipants($roundId) {
    $participants = array();
    
    $query = 'SELECT a.account_id, a.display_name
            FROM tournament_rounds as tr
            LEFT JOIN tournament_participants tp on (tr.tournament_id = tp.tournament_id)
            LEFT JOIN accounts a on (tp.account_id = a.account_id)
            WHERE (tr.round_id = :round_id)';
    
    $values = [':round_id' => $roundId];

    $res = executeSQL($query, $values);

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $participant_item=array(
            "accountId" => $account_id,
            "displayName" => $display_name
        );
    
        array_push($participants, $participant_item);
    }

    return $participants;
}

function getCardsInPack($roundId, $accountId) {
    $cards = array();
    
    $query = 'SELECT mc.card_id, mc.card_name, mc.card_image_uri_low
            FROM pack_cards as pc
            LEFT JOIN magic_cards mc on (pc.card_id = mc.card_id)
            WHERE (pc.round_id = :round_id) AND (pc.account_id = :account_id)';
    
    $values = [':round_id' => $roundId, ':account_id' => $accountId];

    $res = executeSQL($query, $values);

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $card_item=array(
            "cardId" => $card_id,
            "cardName" => $card_name,
            "cardImageUriLow" => $card_image_uri_low
        );
    
        array_push($cards, $card_item);
    }

    return $cards;
}

function getReceivedCardPacks($tournamentId, $accountId, $roundsKeyValuePair) {
    $cards = array();
    
    $query = 'SELECT tr.round_id, mc.card_id,
                mc.card_name, mc.card_image_uri, mc.card_image_uri_back, mc.card_image_uri_low, 
                mc.card_image_uri_low_back, mc.card_type_line, mc.card_mana_cost, mc.card_rarity, mc.card_color_identity
            FROM pack_cards as pc
            LEFT JOIN tournament_rounds tr on (pc.round_id = tr.round_id)
            LEFT JOIN magic_cards mc on (pc.card_id = mc.card_id)
            WHERE (tr.tournament_id = :tournament_id) AND (pc.account_id = :account_id) AND (tr.completed = TRUE)';
    
    $values = [':tournament_id' => $tournamentId, ':account_id' => $accountId];

    $res = executeSQL($query, $values);

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $card_item=array(
            "roundName" => $roundsKeyValuePair[intval($round_id)],
            "cardId" => $card_id,
            "cardName" => $card_name,
            "cardImageUri" => $card_image_uri,
            "cardImageUriLow" => $card_image_uri_low,
            "cardImageUriBack" => $card_image_uri_back,
            "cardImageUriLowBack" => $card_image_uri_low_back,
            "cardTypeLine" => $card_type_line,
            "cardType" => getCardType($card_type_line),
            "cardManaCost" => $card_mana_cost,
            "cardColorIdentity" => $card_color_identity,
            "cardRarity" => $card_rarity,
            "cardRarityNumeric" => rarityToNumber($card_rarity)
        );
    
        array_push($cards, $card_item);
    }

    return $cards;
}

?>