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
    $query = 'SELECT tr.round_id, tr.date_start, tr.date_end, tr.completed
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
            "active" => $currentRoundIndex === $round_id,
            "completed" => boolval($completed)
        );

        array_push($rounds, $roundItem);
    }

    return $rounds;
}

function getCurrentRoundIndex($tournamentId, $pdo) {
    $query = 'SELECT t.active_round_id
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

        return $active_round_id;
    }

    returnError("Tournament not found");
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
        WHERE (tr.tournament_id = :tournament_id) AND (tr.completed = TRUE)';

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

function getTournamentMatchResults($tournamentId, $accountId, $pdo) {
    $ranking = array();
    $POINTS_FOR_MATCH = 3;

    $query = 'SELECT ra.player_id AS player_id, matches_won * :points_for_match AS total_points, ra.display_name AS display_name, SUM(matches_played) AS matches_played, 
            SUM(matches_won) AS matches_won, SUM(games_played) AS games_played, SUM(games_won) AS games_won,
            SUM(matches_won) / SUM(matches_played) AS MWP, SUM(games_won) / SUM(games_played) AS GWP, opponents AS opponents
        FROM (
            SELECT r.player_id AS player_id, r.display_name AS display_name, COUNT(r.match_id) AS matches_played, 
            SUM(r.match_won) AS matches_won, SUM(player_games) + SUM(opponent_games) AS games_played,
            SUM(player_games) AS games_won, GROUP_CONCAT(r.opponent_id) as opponents
            FROM (
                SELECT mr.match_id, m.player_id_1 AS player_id, m.player_id_2 AS opponent_id, mr.player_1_games_won AS player_games, mr.player_2_games_won AS opponent_games,
                    a.display_name AS display_name, mr.player_1_games_won > mr.player_2_games_won AS match_won
                FROM match_results AS mr
                LEFT JOIN matches m on mr.match_id = m.match_id
                LEFT JOIN accounts a on m.player_id_1 = a.account_id
                LEFT JOIN tournament_rounds tr on m.tournament_round_id  = tr.round_id
                WHERE (tr.tournament_id = :tournament_id) AND (mr.result_confirmed = true)
                
                UNION
                
                SELECT mr.match_id, m.player_id_2 AS player_id, m.player_id_1 AS opponent_id, mr.player_2_games_won AS player_games, mr.player_1_games_won AS opponent_games,
                    a.display_name AS display_name, mr.player_2_games_won > mr.player_1_games_won AS match_won
                FROM match_results AS mr
                LEFT JOIN matches m on mr.match_id = m.match_id
                LEFT JOIN accounts a on m.player_id_2 = a.account_id
                LEFT JOIN tournament_rounds tr on m.tournament_round_id  = tr.round_id
                WHERE (tr.tournament_id = :tournament_id) AND (mr.result_confirmed = true)
            ) r
            GROUP BY r.player_id
            
            UNION
            
            SELECT DISTINCT tp.account_id AS player_id, a.display_name AS display_name, 0 AS matches_played, 0 AS matches_won, 0 AS games_played, 0 AS games_won, "" AS opponents
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

        if (strlen($opponents) > 0) {
            $opponentsArray = array_map('intval', explode(',', $opponents));
        }
        else {
            $opponentsArray = [];
        }

        $ranking_item=array(
            "playerId" => $player_id,
            "displayName" => $display_name,
            "totalPoints" => $total_points,
            "matchesPlayed" => $matches_played,
            "matchesWon" => $matches_won,
            "gamesPlayed" => $games_played,
            "gamesWon" => $games_won,
            "MWPOriginal" => floatval($MWP),
            "GWPOriginal" => floatval($GWP),
            "MWP" => number_format($MWP * 100, 2, '.', ''),
            "GWP" => number_format($GWP * 100, 2, '.', ''),
            "opponents" => $opponentsArray,
            "you" => $player_id === $accountId
        );
    
        array_push($ranking, $ranking_item);
    }

    foreach ($ranking as &$participant) {
        if ($participant["opponents"] != null && count($participant["opponents"]) > 0) {
            $sumOpponentMP = 0.0;
            $sumOpponentGP = 0.0;
            
            foreach ($participant["opponents"] as &$opponent) {
                $opponentRank = $ranking[array_search($opponent, array_column($ranking, 'playerId'))];
                $sumOpponentMP += max($opponentRank["MWPOriginal"], 0.3333333333);
                $sumOpponentGP += max($opponentRank["GWPOriginal"], 0.3333333333);
            }
            $participant["OMPOriginal"] = $sumOpponentMP / count($participant["opponents"]);
            $participant["OGPOriginal"] = $sumOpponentGP / count($participant["opponents"]);
            $participant["OMP"] = number_format($participant["OMPOriginal"] * 100, 2, '.', '');
            $participant["OGP"] = number_format($participant["OGPOriginal"] * 100, 2, '.', '');
        }
        else {
            $participant["OMP"] = number_format(0, 2, '.', '');
            $participant["OGP"] = number_format(0, 2, '.', '');
            $participant["OMPOriginal"] = 0.0;
            $participant["OGPOriginal"] = 0.0;
        }
    }

    $ranking = arrayColumnSort('totalPoints', SORT_DESC, SORT_NUMERIC, 
                                  'OMPOriginal', SORT_DESC, SORT_NUMERIC, 
                                  'GWPOriginal', SORT_DESC, SORT_NUMERIC, 
                                  'OGPOriginal', SORT_DESC, SORT_NUMERIC, 
                                  $ranking);
    
    $rank = 1;
    foreach ($ranking as &$participant) {
        $participant["rank"] = $rank++;
    }

    return $ranking;
}

// Call like arrayColumnSort('points', SORT_DESC, SORT_NUMERIC, 'name', SORT_ASC, SORT_STRING, $source);
// Slightly adapted from http://www.php.net/manual/en/function.array-multisort.php#60401
// arrayColumnSort(string $field, [options, ], string $field2, [options, ], .... , $array)
function arrayColumnSort() {
    $args  = func_get_args();
    $array = array_pop($args);
    if (! is_array($array)) return false;
    // Here we'll sift out the values from the columns we want to sort on, and put them in numbered 'subar' ("sub-array") arrays.
    //   (So when sorting by two fields with two modifiers (sort options) each, this will create $subar0 and $subar3)
    foreach($array as $key => $row) // loop through source array
      foreach($args as $akey => $val) // loop through args (fields and modifiers)
        if(is_string($val))             // if the arg's a field, add its value from the source array to a sub-array
          ${"subar$akey"}[$key] = $row[$val];
    // $multisort_args contains the arguments that would (/will) go into array_multisort(): sub-arrays, modifiers and the source array
    $multisort_args = array();
    foreach($args as $key => $val)
      $multisort_args[] = (is_string($val) ? ${"subar$key"} : $val);
    $multisort_args[] = &$array;   // finally add the source array, by reference
    call_user_func_array("array_multisort", $multisort_args);
    return $array;
  }

function getTournamentMatches($tournamentId, $pdo) {
    $matches = array();
    
    $query = 'SELECT m.match_id, m.player_id_1, m.player_id_2, tr.round_id AS round_name,
        p1.mtg_arena_name AS p1_mtg_arena_name, p1.display_name AS p1_display_name, 
        p2.mtg_arena_name AS p2_mtg_arena_name, p2.display_name AS p2_display_name, 
        mr.player_1_games_won AS player_1_games_won, mr.player_2_games_won AS player_2_games_won,
        mr.result_confirmed AS result_confirmed, mr.reporter_account_id AS reporter_account_id,
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_1 THEN mc.card_name ELSE NULL END) SEPARATOR "; ") as cards_traded_to_p1, 
        GROUP_CONCAT((CASE WHEN ct.receiver_account_id = m.player_id_2 THEN mc.card_name ELSE NULL END) SEPARATOR "; ") as cards_traded_to_p2
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
            "roundName" => $round_name,
            "p1MTGArenaName" => $p1_mtg_arena_name,
            "p1DisplayName" => $p1_display_name,
            "p2MTGArenaName" => $p2_mtg_arena_name,
            "p2DisplayName" => $p2_display_name,
            "player1GamesWon" => $player_1_games_won,
            "player2GamesWon" => $player_2_games_won,
            "tradedToP1" => $cards_traded_to_p1,
            "tradedToP2" => $cards_traded_to_p2,
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
        mr.result_confirmed AS result_confirmed, mr.reporter_account_id AS reporter_account_id, 
        GROUP_CONCAT(CASE WHEN ct.receiver_account_id = m.player_id_1 THEN mc.card_name ELSE NULL END) as cards_traded_to_p1, 
        GROUP_CONCAT(CASE WHEN ct.receiver_account_id = m.player_id_2 THEN mc.card_name ELSE NULL END) as cards_traded_to_p2
        FROM matches AS m
        LEFT JOIN accounts p1 on (m.player_id_1 = p1.account_id)
        LEFT JOIN accounts p2 on (m.player_id_2 = p2.account_id)
        LEFT JOIN match_results mr on (m.match_id = mr.match_id)
        LEFT JOIN card_trades ct on (m.match_id = ct.match_id)
        LEFT JOIN magic_cards mc on (ct.card_id = mc.card_id)
        WHERE (m.tournament_round_id = :tournament_round_id) AND ((m.player_id_1 = :player_id) OR (m.player_id_2 = :player_id))
        GROUP BY m.match_id';

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
            "tradedToP1" => $cards_traded_to_p1,
            "tradedToP2" => $cards_traded_to_p2,
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