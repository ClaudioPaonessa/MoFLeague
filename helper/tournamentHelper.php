<?php

require_once '../../helper/errorHelper.php';

function getTournamentName($tournamentId, $pdo) {
    $query = 'SELECT t.tournament_name
        FROM tournaments AS t
        WHERE (t.tournament_id = :tournament_id)';

    $values = [':tournament_id' => $tournamentId];

    try
    {
        $res = $pdo->prepare($query);
        $res->execute($values);
    }
    catch (PDOException $e)
    {
        returnError("Error in SQL query.");
    }

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        return $tournament_name;
    }

    return "";
}

function getRounds($tournamentId, $currentRoundIndex, $pdo) {
    $query = 'SELECT tr.round_id, tr.date_start, tr.date_end
    FROM tournament_rounds AS tr
    WHERE (tournament_id = :tournament_id)
    ORDER BY tr.date_start ASC';

    $values = [':tournament_id' => $tournamentId];

    $rounds = array();

    try
    {
        $res = $pdo->prepare($query);
        $res->execute($values);
    }
    catch (PDOException $e)
    {
        returnError("Error in SQL query.");
    }

    $i = 1;

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $roundItem=array(
            "roundId" => $round_id,
            "name" => "Round " . $i++,
            "dateStart" =>  (new DateTime($date_start, new DateTimeZone("Europe/Zurich")))->format('Y-m-d'),
            "dateEnd" => (new DateTime($date_end, new DateTimeZone("Europe/Zurich")))->format('Y-m-d'),
            "active" => $currentRoundIndex === $round_id
        );

        array_push($rounds, $roundItem);
    }

    return $rounds;
}

function getCurrentRoundIndex($tournamentId, $pdo) {
    $query = 'SELECT tr.round_id
        FROM tournament_rounds AS tr
        WHERE (tr.tournament_id = :tournament_id) AND (tr.date_start <= CURDATE()) AND (tr.date_end >= CURDATE())';

    $values = [':tournament_id' => $tournamentId];

    try
    {
        $res = $pdo->prepare($query);
        $res->execute($values);
    }
    catch (PDOException $e)
    {
        returnError("Error in SQL query.");
    }

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        return $round_id;
    }

    return -1;
}

function getNumberOfRounds($tournamentId, $pdo) {
    $query = 'SELECT COUNT(1) AS count
        FROM tournament_rounds AS tr
        WHERE (tr.tournament_id = :tournament_id)';

    $values = [':tournament_id' => $tournamentId];

    try
    {
        $res = $pdo->prepare($query);
        $res->execute($values);
    }
    catch (PDOException $e)
    {
        returnError("Error in SQL query.");
    }

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        return $count;
    }

    return 0;
}

function getCurrentRoundsFinished($tournamentId, $pdo) {
    $query = 'SELECT COUNT(1) AS count
        FROM tournament_rounds AS tr
        WHERE (tr.tournament_id = :tournament_id) AND (tr.date_start <= CURDATE()) AND (tr.date_end < CURDATE())';

    $values = [':tournament_id' => $tournamentId];

    try
    {
        $res = $pdo->prepare($query);
        $res->execute($values);
    }
    catch (PDOException $e)
    {
        returnError("Error in SQL query.");
    }

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        return $count;
    }

    return 0;
}

function getCurrentMatches($roundId, $pdo) {
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

    try
    {
        $res = $pdo->prepare($query);
        $res->execute($values);
    }
    catch (PDOException $e)
    {
        returnError("Error in SQL query.");
    }

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

function getTournamentMatchResults($tournamentId, $pdo) {
    $ranking = array();
    $POINTS_FOR_MATCH = 3;
    
    $query = 'SELECT ra.player_id AS player_id, matches_won * :points_for_match AS total_points, ra.display_name AS display_name, SUM(matches_played) AS matches_played, 
            SUM(matches_won) AS matches_won, SUM(games_played) AS games_played, SUM(games_won) AS games_won 
        FROM (
            SELECT r.player_id AS player_id, r.display_name AS display_name, COUNT(r.match_id) AS matches_played, 
            SUM(r.match_won) AS matches_won, SUM(player_games) + SUM(opponent_games) AS games_played,
            SUM(player_games) AS games_won
            FROM (
                SELECT mr.match_id, m.player_id_1 AS player_id, mr.player_1_games_won AS player_games, mr.player_2_games_won AS opponent_games,
                    a.display_name AS display_name, mr.player_1_games_won > mr.player_2_games_won AS match_won
                FROM match_results AS mr
                LEFT JOIN matches m on mr.match_id = m.match_id
                LEFT JOIN accounts a on m.player_id_1 = a.account_id
                LEFT JOIN tournament_rounds tr on m.tournament_round_id  = tr.round_id
                WHERE (tr.tournament_id = :tournament_id) AND (mr.result_confirmed = true)
                
                UNION
                
                SELECT mr.match_id, m.player_id_2 AS player_id, mr.player_2_games_won AS player_games, mr.player_1_games_won AS opponent_games,
                    a.display_name AS display_name, mr.player_2_games_won > mr.player_1_games_won AS match_won
                FROM match_results AS mr
                LEFT JOIN matches m on mr.match_id = m.match_id
                LEFT JOIN accounts a on m.player_id_2 = a.account_id
                LEFT JOIN tournament_rounds tr on m.tournament_round_id  = tr.round_id
                WHERE (tr.tournament_id = :tournament_id) AND (mr.result_confirmed = true)
            ) r
            GROUP BY r.player_id
            
            UNION
            
            SELECT DISTINCT tp.account_id AS player_id, a.display_name AS display_name, 0 AS matches_played, 0 AS matches_won, 0 AS games_played, 0 AS games_won
            FROM tournament_participants AS tp
            LEFT JOIN accounts a ON tp.account_id = a.account_id
            WHERE (tp.tournament_id = :tournament_id)
        ) ra
        GROUP BY ra.player_id
        ORDER BY total_points DESC';

    $values = [':tournament_id' => $tournamentId, ':points_for_match' => $POINTS_FOR_MATCH];

    try
    {
        $res = $pdo->prepare($query);
        $res->execute($values);
    }
    catch (PDOException $e)
    {
        returnError("Error in SQL query.");
    }

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $ranking_item=array(
            "playerId" => $player_id,
            "displayName" => $display_name,
            "totalPoints" => $total_points,
            "matchesPlayed" => $matches_played,
            "matchesWon" => $matches_won,
            "gamesPlayed" => $games_played,
            "gamesWon" => $games_won
        );
    
        array_push($ranking, $ranking_item);
    }

    return $ranking;
}

function getTournamentMatches($tournamentId, $pdo) {
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
        LEFT JOIN tournament_rounds tr on (m.tournament_round_id = tr.round_id)
        WHERE (tr.tournament_id = :tournament_id)';

    $values = [':tournament_id' => $tournamentId];

    try
    {
        $res = $pdo->prepare($query);
        $res->execute($values);
    }
    catch (PDOException $e)
    {
        returnError("Error in SQL query.");
    }

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

function getCurrentMatchesFiltered($roundId, $accountId, $pdo) {
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
        WHERE (m.tournament_round_id = :tournament_round_id) AND ((m.player_id_1 = :player_id) OR (m.player_id_2 = :player_id))';

    $values = [':tournament_round_id' => $roundId, ':player_id' => $accountId];

    try
    {
        $res = $pdo->prepare($query);
        $res->execute($values);
    }
    catch (PDOException $e)
    {
        returnError("Error in SQL query.");
    }

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
            "resultConfirmed" => boolval($result_confirmed),
            "reporterYou" => $reporter_account_id == $accountId
        );
    
        array_push($matches, $match_item);
    }

    return $matches;
}

function getCardSetId($tournamentId, $pdo) {    
    $query = 'SELECT t.set_id
        FROM tournaments AS t
        WHERE (t.tournament_id = :tournament_id)';

    $values = [':tournament_id' => $tournamentId];  

    try
    {
        $res = $pdo->prepare($query);
        $res->execute($values);
    }
    catch (PDOException $e)
    {
        returnError("Error in SQL query.");
    }

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        return $set_id;
    }

    returnError("No tournament with this id found.");
}

function getSetCards($setId, $pdo) {
    $cards = array();
    
    $query = 'SELECT c.card_id, c.card_name, c.card_image_uri
        FROM magic_cards AS c
        WHERE (c.magic_set_id = :magic_set_id)';

    $values = [':magic_set_id' => $setId];

    try
    {
        $res = $pdo->prepare($query);
        $res->execute($values);
    }
    catch (PDOException $e)
    {
        returnError("Error in SQL query.");
    }

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