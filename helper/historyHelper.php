<?php

require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';

function getMatchHistoryForPlayer($accountId) {
    $matches = array();

    $query = 'SELECT t.tournament_id, m.match_id, t.tournament_name, m.player_id_1, m.player_id_2,
        p1.mtg_arena_name AS p1_mtg_arena_name, p1.display_name AS p1_display_name, 
        p2.mtg_arena_name AS p2_mtg_arena_name, p2.display_name AS p2_display_name, 
        mr.player_1_games_won AS player_1_games_won, mr.player_2_games_won AS player_2_games_won,
        mr.result_confirmed AS result_confirmed, mr.reporter_account_id AS reporter_account_id, 
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_1 THEN mc.card_name ELSE NULL END) SEPARATOR ";") as cards_traded_to_p1, 
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_2 THEN mc.card_name ELSE NULL END) SEPARATOR ";") as cards_traded_to_p2,
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_1 THEN mc.card_image_uri ELSE NULL END) SEPARATOR ";") as cards_traded_to_p1_images, 
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_2 THEN mc.card_image_uri ELSE NULL END) SEPARATOR ";") as cards_traded_to_p2_images
        FROM matches AS m
        LEFT JOIN accounts p1 on (m.player_id_1 = p1.account_id)
        LEFT JOIN accounts p2 on (m.player_id_2 = p2.account_id)
        LEFT JOIN match_results mr on (m.match_id = mr.match_id)
        LEFT JOIN card_trades ct on (m.match_id = ct.match_id)
        LEFT JOIN magic_cards mc on (ct.card_id = mc.card_id)
        LEFT JOIN tournament_rounds tr on (m.tournament_round_id = tr.round_id)
        LEFT JOIN tournaments t on (tr.tournament_id = t.tournament_id)
        WHERE ((m.player_id_1 = :player_id) OR (m.player_id_2 = :player_id)) AND (mr.result_confirmed = TRUE)
        GROUP BY m.match_id';

    $values = [':player_id' => $accountId];

    $res = executeSQL($query, $values);

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $match_item=array(
            "matchId" => $match_id,
            "tournamentName" => $tournament_name,
            "playerId1" => $player_id_1,
            "playerId2" => $player_id_2,
            "p1MTGArenaName" => $p1_mtg_arena_name,
            "p1DisplayName" => $p1_display_name,
            "p2MTGArenaName" => $p2_mtg_arena_name,
            "p2DisplayName" => $p2_display_name,
            "player1GamesWon" => $player_1_games_won,
            "player2GamesWon" => $player_2_games_won,
            "tradedToP1" => explode(";", $cards_traded_to_p1),
            "tradedToP2" => explode(";", $cards_traded_to_p2),
            "tradedToP1Images" => explode(";", $cards_traded_to_p1_images),
            "tradedToP2Images" => explode(";", $cards_traded_to_p2_images),
            "resultConfirmed" => boolval($result_confirmed),
            "reporterYou" => $reporter_account_id == $accountId
        );
    
        array_push($matches, $match_item);
    }

    return $matches;
}

?>