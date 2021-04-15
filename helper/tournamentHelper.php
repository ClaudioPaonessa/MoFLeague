<?php

require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';

function getTournamentName($tournamentId) {
    $query = 'SELECT t.tournament_name
        FROM tournaments AS t
        WHERE (t.tournament_id = :tournament_id)';

    $values = [':tournament_id' => $tournamentId];

    $res = executeSQL($query, $values);
    $row = $res->fetch(PDO::FETCH_ASSOC);
    
    if (is_array($row)) {
        extract($row);
        return $tournament_name;
    }
    
    returnError("Tournament not found");
}

function getTournamentGroupSize($tournamentId) {
    $query = 'SELECT t.group_size
        FROM tournaments AS t
        WHERE (t.tournament_id = :tournament_id)';

    $values = [':tournament_id' => $tournamentId];

    $res = executeSQL($query, $values);
    $row = $res->fetch(PDO::FETCH_ASSOC);
    
    if (is_array($row)) {
        extract($row);
        return intval($group_size);
    }
    
    returnError("Tournament not found");
}

function getTournamentFromRound($roundId) {
    $query = 'SELECT tr.tournament_id
        FROM tournament_rounds AS tr
        WHERE (tr.round_id = :round_id)';

    $values = [':round_id' => $roundId];

    $res = executeSQL($query, $values);
    $row = $res->fetch(PDO::FETCH_ASSOC);
    
    if (is_array($row)) {
        extract($row);
        return intval($tournament_id);
    }
    
    returnError("Round not found");
}

function getTournamentMatchesPerRound($tournamentId) {
    $query = 'SELECT t.matches_per_round
        FROM tournaments AS t
        WHERE (t.tournament_id = :tournament_id)';

    $values = [':tournament_id' => $tournamentId];

    $res = executeSQL($query, $values);
    $row = $res->fetch(PDO::FETCH_ASSOC);
    
    if (is_array($row)) {
        extract($row);
        return intval($matches_per_round);
    }
    
    returnError("Tournament not found");
}

function getRounds($tournamentId, $currentRoundIndex) {
    $query = 'SELECT tr.round_id, tr.date_start, tr.date_end, tr.completed
    FROM tournament_rounds AS tr
    WHERE (tournament_id = :tournament_id)
    ORDER BY tr.date_start ASC';

    $values = [':tournament_id' => $tournamentId];

    $res = executeSQL($query, $values);

    $rounds = array();   

    $i = 1;

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $roundItem=array(
            "roundId" => $round_id,
            "name" => "Round " . $i++,
            "dateStart" =>  (new DateTime($date_start, new DateTimeZone("Europe/Zurich")))->format('Y-m-d'),
            "dateEnd" => (new DateTime($date_end, new DateTimeZone("Europe/Zurich")))->format('Y-m-d'),
            "active" => $currentRoundIndex === $round_id,
            "completed" => boolval($completed)
        );

        array_push($rounds, $roundItem);
    }

    return $rounds;
}

function getAllRounds($tournamentId) {
    $query = 'SELECT tr.round_id, tr.date_start, tr.date_end, tr.completed
    FROM tournament_rounds AS tr
    WHERE (tournament_id = :tournament_id)
    ORDER BY tr.date_start ASC';

    $values = [':tournament_id' => $tournamentId];

    $res = executeSQL($query, $values);

    $rounds = array();   

    $i = 1;

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $roundItem=array(
            "roundId" => $round_id,
            "name" => "Round " . $i++,
            "dateStart" =>  (new DateTime($date_start, new DateTimeZone("Europe/Zurich")))->format('Y-m-d'),
            "dateEnd" => (new DateTime($date_end, new DateTimeZone("Europe/Zurich")))->format('Y-m-d'),
            "completed" => boolval($completed)
        );

        array_push($rounds, $roundItem);
    }

    return $rounds;
}

function getCompletedRounds($tournamentId) {
    $query = 'SELECT tr.round_id, tr.date_start, tr.date_end
    FROM tournament_rounds AS tr
    WHERE (tournament_id = :tournament_id) AND (completed = TRUE)
    ORDER BY tr.date_start ASC';

    $values = [':tournament_id' => $tournamentId];

    $res = executeSQL($query, $values);

    $completedRounds = array();

    $i = 1;

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $roundItem=array(
            "roundId" => $round_id,
            "name" => "Round " . $i++,
            "dateStart" =>  $date_start,
            "dateEnd" => $date_end
        );

        array_push($completedRounds, $roundItem);
    }

    return $completedRounds;
}

function getPreviousCompletedRoundsForRanking($tournamentId, $round) {
    $query = 'SELECT tr.round_id, tr.date_start, tr.date_end, tr.completed
    FROM tournament_rounds AS tr
    WHERE (tournament_id = :tournament_id) AND (completed = TRUE) AND (date_end < :round_date_start)
    ORDER BY tr.date_start ASC';

    $values = [':tournament_id' => $tournamentId, ':round_date_start' => $round["dateStart"]];

    $res = executeSQL($query, $values);

    $previousCompletedRounds = array();
    array_push($previousCompletedRounds, intval($round["roundId"]));

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        array_push($previousCompletedRounds, intval($round_id));
    }
    
    return $previousCompletedRounds;
}

function getRoundStartDate($roundId) {
    $query = 'SELECT tr.round_id, tr.date_start
            FROM tournament_rounds AS tr
            WHERE tr.round_id = :round_id';
    
    $values = [':round_id' => $roundId];

    $res = executeSQL($query, $values);
    $row = $res->fetch(PDO::FETCH_ASSOC);
    
    if (is_array($row)) {
        extract($row);
        return $date_start;
    }
    
    returnError("Round not found");
}

function getPreviousCompletedRounds($tournamentId, $roundDateStart) {
    $query = 'SELECT tr.round_id, tr.date_start, tr.date_end, tr.completed
    FROM tournament_rounds AS tr
    WHERE (tournament_id = :tournament_id) AND (completed = TRUE) AND (date_end < :round_date_start)
    ORDER BY tr.date_start ASC';

    $values = [':tournament_id' => $tournamentId, ':round_date_start' => $roundDateStart];

    $res = executeSQL($query, $values);

    $previousCompletedRounds = array();

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        array_push($previousCompletedRounds, intval($round_id));
    }
    
    return $previousCompletedRounds;
}

function getRoundsKeyValuePair($rounds) {
    $roundsKeyValuePair = array();

    foreach ($rounds as &$round) {
        $roundsKeyValuePair[intval($round["roundId"])] = $round["name"];
    }

    return $roundsKeyValuePair;
}

function getCurrentRoundIndex($tournamentId) {
    $query = 'SELECT t.active_round_id
        FROM tournaments AS t
        WHERE (t.tournament_id = :tournament_id)';

    $values = [':tournament_id' => $tournamentId];

    $res = executeSQL($query, $values);
    $row = $res->fetch(PDO::FETCH_ASSOC);
    
    if (is_array($row)) {
        extract($row);
        return $active_round_id;
    }
    
    returnError("Tournament not found");
}

function getCurrentRound($tournamentId, $roundsKeyValuePair) {
    $roundId = getCurrentRoundIndex($tournamentId);
    
    return $roundsKeyValuePair[$roundId];
}

function getNumberOfRounds($tournamentId) {
    $query = 'SELECT COUNT(1) AS count
        FROM tournament_rounds AS tr
        WHERE (tr.tournament_id = :tournament_id)';

    $values = [':tournament_id' => $tournamentId];

    $res = executeSQL($query, $values);
    $row = $res->fetch(PDO::FETCH_ASSOC);

    if (is_array($row)) {
        extract($row);
        return $count;
    }
    
    return 0;
}

function getCurrentRoundsFinished($tournamentId) {
    $query = 'SELECT COUNT(1) AS count
        FROM tournament_rounds AS tr
        WHERE (tr.tournament_id = :tournament_id) AND (tr.completed = TRUE)';

    $values = [':tournament_id' => $tournamentId];

    $res = executeSQL($query, $values);
    $row = $res->fetch(PDO::FETCH_ASSOC);

    if (is_array($row)) {
        extract($row);
        return $count;
    }
    
    return 0;
}

function getCurrentMatches($roundId) {
    $matches = array();
    
    $query = 'SELECT m.match_id, m.player_id_1, m.player_id_2, 
        p1.mtg_arena_name AS p1_mtg_arena_name, p1.display_name AS p1_display_name, 
        p2.mtg_arena_name AS p2_mtg_arena_name, p2.display_name AS p2_display_name, 
        mr.player_1_games_won AS player_1_games_won, mr.player_2_games_won AS player_2_games_won,
        mr.result_confirmed AS result_confirmed, mr.reporter_account_id AS reporter_account_id
        FROM matches AS m
        LEFT JOIN accounts p1 on (m.player_id_1 = p1.account_id)
        LEFT JOIN accounts p2 on (m.player_id_2 = p2.account_id)
        LEFT JOIN match_results mr on (m.match_id = mr.match_id)
        WHERE (m.tournament_round_id = :tournament_round_id)';

    $values = [':tournament_round_id' => $roundId];

    $res = executeSQL($query, $values);

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $match_item=array(
            "matchId" => $match_id,
            "playerId1" => $player_id_1,
            "playerId2" => $player_id_2,
            "p1MTGArenaName" => $p1_mtg_arena_name,
            "p1DisplayName" => $p1_display_name,
            "p2MTGArenaName" => $p2_mtg_arena_name,
            "p2DisplayName" => $p2_display_name,
            "player1GamesWon" => $player_1_games_won,
            "player2GamesWon" => $player_2_games_won,
            "resultConfirmed" => boolval($result_confirmed)
        );
    
        array_push($matches, $match_item);
    }

    return $matches;
}

function getTournamentMatches($tournamentId, $roundsKeyValuePair) {
    $matches = array();
    
    $query = 'SELECT m.match_id, m.player_id_1, m.player_id_2, tr.round_id AS round_id,
        p1.mtg_arena_name AS p1_mtg_arena_name, p1.display_name AS p1_display_name, 
        p2.mtg_arena_name AS p2_mtg_arena_name, p2.display_name AS p2_display_name, 
        mr.player_1_games_won AS player_1_games_won, mr.player_2_games_won AS player_2_games_won,
        mr.result_confirmed AS result_confirmed, mr.reporter_account_id AS reporter_account_id,
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_1 THEN mc.card_name ELSE NULL END) SEPARATOR ";") as cards_traded_to_p1, 
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_2 THEN mc.card_name ELSE NULL END) SEPARATOR ";") as cards_traded_to_p2,
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_1 THEN mc.card_image_uri ELSE NULL END) SEPARATOR ";") as cards_traded_to_p1_images, 
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_2 THEN mc.card_image_uri ELSE NULL END) SEPARATOR ";") as cards_traded_to_p2_images,
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_1 THEN mc.card_rarity ELSE NULL END) SEPARATOR ";") as cards_traded_to_p1_rarities, 
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_2 THEN mc.card_rarity ELSE NULL END) SEPARATOR ";") as cards_traded_to_p2_rarities
        FROM matches AS m
        LEFT JOIN accounts p1 on (m.player_id_1 = p1.account_id)
        LEFT JOIN accounts p2 on (m.player_id_2 = p2.account_id)
        LEFT JOIN match_results mr on (m.match_id = mr.match_id)
        LEFT JOIN tournament_rounds tr on (m.tournament_round_id = tr.round_id)
        LEFT JOIN card_trades ct on (m.match_id = ct.match_id)
        LEFT JOIN magic_cards mc on (ct.card_id = mc.card_id)
        WHERE (tr.tournament_id = :tournament_id)
        GROUP BY m.match_id
        ORDER BY tr.date_start ASC';

    $values = [':tournament_id' => $tournamentId];

    $res = executeSQL($query, $values);

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $match_item=array(
            "matchId" => $match_id,
            "playerId1" => $player_id_1,
            "playerId2" => $player_id_2,
            "roundName" => $roundsKeyValuePair[$round_id],
            "p1MTGArenaName" => $p1_mtg_arena_name,
            "p1DisplayName" => $p1_display_name,
            "p2MTGArenaName" => $p2_mtg_arena_name,
            "p2DisplayName" => $p2_display_name,
            "player1GamesWon" => $player_1_games_won,
            "player2GamesWon" => $player_2_games_won,
            "tradedToP1" => empty($cards_traded_to_p1) ? [] : explode(";", $cards_traded_to_p1),
            "tradedToP2" => empty($cards_traded_to_p2) ? [] : explode(";", $cards_traded_to_p2),
            "tradedToP1Images" => explode(";", $cards_traded_to_p1_images),
            "tradedToP2Images" => explode(";", $cards_traded_to_p2_images),
            "tradedToP1Rarities" => explode(";", $cards_traded_to_p1_rarities),
            "tradedToP2Rarities" => explode(";", $cards_traded_to_p2_rarities),
            "resultConfirmed" => boolval($result_confirmed)
        );
    
        array_push($matches, $match_item);
    }

    return $matches;
}

function getCurrentMatchesFiltered($roundId, $accountId) {
    $matches = array();

    $query = 'SELECT m.match_id, m.player_id_1, m.player_id_2,
        p1.mtg_arena_name AS p1_mtg_arena_name, p1.display_name AS p1_display_name, 
        p2.mtg_arena_name AS p2_mtg_arena_name, p2.display_name AS p2_display_name, 
        mr.player_1_games_won AS player_1_games_won, mr.player_2_games_won AS player_2_games_won,
        mr.result_confirmed AS result_confirmed, mr.reporter_account_id AS reporter_account_id, 
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_1 THEN mc.card_name ELSE NULL END) SEPARATOR ";") as cards_traded_to_p1, 
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_2 THEN mc.card_name ELSE NULL END) SEPARATOR ";") as cards_traded_to_p2,
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_1 THEN mc.card_image_uri ELSE NULL END) SEPARATOR ";") as cards_traded_to_p1_images, 
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_2 THEN mc.card_image_uri ELSE NULL END) SEPARATOR ";") as cards_traded_to_p2_images,
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_1 THEN mc.card_rarity ELSE NULL END) SEPARATOR ";") as cards_traded_to_p1_rarities, 
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_2 THEN mc.card_rarity ELSE NULL END) SEPARATOR ";") as cards_traded_to_p2_rarities
        FROM matches AS m
        LEFT JOIN accounts p1 on (m.player_id_1 = p1.account_id)
        LEFT JOIN accounts p2 on (m.player_id_2 = p2.account_id)
        LEFT JOIN match_results mr on (m.match_id = mr.match_id)
        LEFT JOIN card_trades ct on (m.match_id = ct.match_id)
        LEFT JOIN magic_cards mc on (ct.card_id = mc.card_id)
        WHERE (m.tournament_round_id = :tournament_round_id) AND ((m.player_id_1 = :player_id) OR (m.player_id_2 = :player_id))
        GROUP BY m.match_id';

    $values = [':tournament_round_id' => $roundId, ':player_id' => $accountId];

    $res = executeSQL($query, $values);

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $match_item=array(
            "matchId" => $match_id,
            "playerId1" => $player_id_1,
            "playerId2" => $player_id_2,
            "p1MTGArenaName" => $p1_mtg_arena_name,
            "p1DisplayName" => $p1_display_name,
            "p2MTGArenaName" => $p2_mtg_arena_name,
            "p2DisplayName" => $p2_display_name,
            "player1GamesWon" => $player_1_games_won,
            "player2GamesWon" => $player_2_games_won,
            "tradedToP1" => empty($cards_traded_to_p1) ? [] : explode(";", $cards_traded_to_p1),
            "tradedToP2" => empty($cards_traded_to_p2) ? [] : explode(";", $cards_traded_to_p2),
            "tradedToP1Images" => explode(";", $cards_traded_to_p1_images),
            "tradedToP2Images" => explode(";", $cards_traded_to_p2_images),
            "tradedToP1Rarities" => explode(";", $cards_traded_to_p1_rarities),
            "tradedToP2Rarities" => explode(";", $cards_traded_to_p2_rarities),
            "resultConfirmed" => boolval($result_confirmed),
            "reporterYou" => $reporter_account_id == $accountId
        );
    
        array_push($matches, $match_item);
    }

    return $matches;
}

function getMatchesFiltered($tournamentId, $accountId, $roundsKeyValuePair) {
    $matches = array();

    $query = 'SELECT m.match_id, m.player_id_1, m.player_id_2, tr.round_id AS round_id,
        p1.mtg_arena_name AS p1_mtg_arena_name, p1.display_name AS p1_display_name, 
        p2.mtg_arena_name AS p2_mtg_arena_name, p2.display_name AS p2_display_name, 
        mr.player_1_games_won AS player_1_games_won, mr.player_2_games_won AS player_2_games_won,
        mr.result_confirmed AS result_confirmed, mr.reporter_account_id AS reporter_account_id, 
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_1 THEN mc.card_name ELSE NULL END) SEPARATOR ";") as cards_traded_to_p1, 
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_2 THEN mc.card_name ELSE NULL END) SEPARATOR ";") as cards_traded_to_p2,
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_1 THEN mc.card_image_uri ELSE NULL END) SEPARATOR ";") as cards_traded_to_p1_images, 
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_2 THEN mc.card_image_uri ELSE NULL END) SEPARATOR ";") as cards_traded_to_p2_images,
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_1 THEN mc.card_rarity ELSE NULL END) SEPARATOR ";") as cards_traded_to_p1_rarities, 
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_2 THEN mc.card_rarity ELSE NULL END) SEPARATOR ";") as cards_traded_to_p2_rarities
        FROM matches AS m
        LEFT JOIN accounts p1 on (m.player_id_1 = p1.account_id)
        LEFT JOIN accounts p2 on (m.player_id_2 = p2.account_id)
        LEFT JOIN match_results mr on (m.match_id = mr.match_id)
        LEFT JOIN tournament_rounds tr on (m.tournament_round_id = tr.round_id)
        LEFT JOIN card_trades ct on (m.match_id = ct.match_id)
        LEFT JOIN magic_cards mc on (ct.card_id = mc.card_id)
        WHERE ((m.player_id_1 = :player_id) OR (m.player_id_2 = :player_id)) AND (tr.tournament_id = :tournament_id) 
        GROUP BY m.match_id';

    $values = [':player_id' => $accountId, ':tournament_id' => $tournamentId];

    $res = executeSQL($query, $values);

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $match_item=array(
            "matchId" => $match_id,
            "playerId1" => $player_id_1,
            "playerId2" => $player_id_2,
            "roundName" => $roundsKeyValuePair[$round_id],
            "p1MTGArenaName" => $p1_mtg_arena_name,
            "p1DisplayName" => $p1_display_name,
            "p2MTGArenaName" => $p2_mtg_arena_name,
            "p2DisplayName" => $p2_display_name,
            "player1GamesWon" => $player_1_games_won,
            "player2GamesWon" => $player_2_games_won,
            "tradedToP1" => empty($cards_traded_to_p1) ? [] : explode(";", $cards_traded_to_p1),
            "tradedToP2" => empty($cards_traded_to_p2) ? [] : explode(";", $cards_traded_to_p2),
            "tradedToP1Images" => explode(";", $cards_traded_to_p1_images),
            "tradedToP2Images" => explode(";", $cards_traded_to_p2_images),
            "tradedToP1Rarities" => explode(";", $cards_traded_to_p1_rarities),
            "tradedToP2Rarities" => explode(";", $cards_traded_to_p2_rarities),
            "resultConfirmed" => boolval($result_confirmed),
            "reporterYou" => $reporter_account_id == $accountId
        );
    
        array_push($matches, $match_item);
    }

    return $matches;
}

function getCardSetId($tournamentId) {    
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

    returnError("No Tournament found");
}

function getSetCards($setId) {
    $cards = array();
    
    $query = 'SELECT c.card_id, c.card_name, c.card_image_uri
        FROM magic_cards AS c
        WHERE (c.magic_set_id = :magic_set_id)';

    $values = [':magic_set_id' => $setId];

    $res = executeSQL($query, $values);

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $cards_item=array(
            "cardId" => $card_id,
            "cardName" => $card_name,
            "cardImageUri" => $card_image_uri
        );
    
        array_push($cards, $cards_item);
    }

    return $cards;
}

?>