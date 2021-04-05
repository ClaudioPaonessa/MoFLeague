<?php

require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';

function getInitialCardPool($tournamentId, $accountId) {
    $cards = array();
    
    $query = 'SELECT cp.card_id, mc.card_name, mc.card_image_uri, mc.card_type_line, mc.card_mana_cost, mc.card_rarity, mc.card_color_identity, COUNT(*) as number_of_cards
        FROM initial_card_pool AS cp
        LEFT JOIN magic_cards mc on (cp.card_id = mc.card_id)
        WHERE (cp.tournament_id = :tournament_id) AND (cp.account_id = :account_id)
        GROUP BY cp.card_id';

    $values = [':tournament_id' => $tournamentId, ':account_id' => $accountId];

    $res = executeSQL($query, $values);

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $card_item=array(
            "cardId" => $card_id,
            "cardName" => $card_name,
            "numberOfCards" => $number_of_cards,
            "cardImageUri" => $card_image_uri,
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

function sharePool($tournamentId, $accountId, $pin) {
    $query = 'UPDATE tournament_participants 
            SET pool_public = TRUE, pool_pin_code = :pin
            WHERE (tournament_id = :tournament_id) AND (account_id = :account_id)';
    
    $values = [':tournament_id' => $tournamentId, ':account_id' => $accountId, ':pin' => $pin];

    executeSQL($query, $values);
}

function stopSharingPool($tournamentId, $accountId) {
    $query = 'UPDATE tournament_participants 
            SET pool_public = False
            WHERE (tournament_id = :tournament_id) AND (account_id = :account_id)';
    
    $values = [':tournament_id' => $tournamentId, ':account_id' => $accountId];

    executeSQL($query, $values);
}

function getShareStatus($tournamentId, $accountId) {
    $query = 'SELECT tp.account_id, tp.pool_public, tp.pool_pin_code
            FROM tournament_participants AS tp
            WHERE (tp.tournament_id = :tournament_id) AND (tp.account_id = :account_id)';
    
    $values = [':tournament_id' => $tournamentId, ':account_id' => $accountId];
    
    $res = executeSQL($query, $values);
    $row = $res->fetch(PDO::FETCH_ASSOC);

    if (is_array($row)) {
        extract($row);

        $shareStatus=array(
            "accountId" => $account_id,
            "poolPublic" => boolval($pool_public),
            "poolPinCode" => $pool_pin_code
        );

        return $shareStatus;
    }

    returnError("Tournament or participant not found.");
}

function checkPin($tournamentId, $accountId, $pin) {
    $shareStatus = getShareStatus($tournamentId, $accountId);

    if (!$shareStatus['poolPublic']) {
        return FALSE;
    }

    return ($shareStatus['poolPinCode'] == $pin);
}

function resetCardPool($tournamentId, $accountId) {    
    $query = 'DELETE FROM initial_card_pool
        WHERE (tournament_id = :tournament_id) AND (account_id = :account_id)';

    $values = [':tournament_id' => $tournamentId, ':account_id' => $accountId];

    executeSQL($query, $values);
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
    if (strpos($card_type_line, "Land") !== false) {
        return "Land";
    }

    if (strpos($card_type_line, "Creature") !== false) {
        return "Creature";
    }

    if (strpos($card_type_line, "Artifact") !== false) {
        return "Artifact";
    }

    if (strpos($card_type_line, "Enchantment") !== false) {
        return "Enchantment";
    }

    if (strpos($card_type_line, "Instant") !== false) {
        return "Instant";
    }

    if (strpos($card_type_line, "Sorcery") !== false) {
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