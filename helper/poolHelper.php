<?php

require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';

function getCardPool($tournamentId, $accountId) {
    $cards = array();
    
    $query = 'SELECT cp.card_id, mc.card_name, mc.card_image_uri, mc.card_type_line, mc.card_mana_cost, mc.card_rarity, COUNT(*) as number_of_cards
        FROM initial_card_pool AS cp
        LEFT JOIN magic_cards mc on (cp.card_id = mc.card_id)
        WHERE (cp.tournament_id = :tournament_id) AND (cp.account_id = :account_id)
        GROUP BY cp.card_id';

    $values = [':tournament_id' => $tournamentId, ':account_id' => $accountId];

    $res = executeSQL($query, $values);

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $card_item=array(
            "cardName" => $card_name,
            "numberOfCards" => $number_of_cards,
            "cardImageUri" => $card_image_uri,
            "cardTypeLine" => $card_type_line,
            "cardType" => getCardType($card_type_line),
            "cardManaCost" => $card_mana_cost,
            "cardRarity" => $card_rarity,
            "cardRarityNumeric" => rarityToNumber($card_rarity)
        );
    
        array_push($cards, $card_item);
    }

    return $cards;
}

function rarityToNumber($cardRarity) {
    switch ($cardRarity) {
        case "common":
            return 0;
            break;
        case "uncommon":
            return 1;
            break;
        case "rare":
            return 2;
            break;
        case "mythic":
            return 3;
            break;
    }
}

function getCardType($card_type_line) {
    if (str_contains($card_type_line, "Land")) {
        return "Land";
    }

    if (str_contains($card_type_line, "Creature")) {
        return "Creature";
    }

    if (str_contains($card_type_line, "Artifact")) {
        return "Artifact";
    }

    if (str_contains($card_type_line, "Enchantment")) {
        return "Enchantment";
    }

    if (str_contains($card_type_line, "Instant")) {
        return "Instant";
    }

    if (str_contains($card_type_line, "Sorcery")) {
        return "Sorcery";
    }

    return "Other";
}

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