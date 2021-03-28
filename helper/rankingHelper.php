<?php

function getLiveRanking($tournamentId, $accountId, $groupSize) {
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

    return getRanking($accountId, $query, $values, $groupSize);
}

function getRankingFromRounds($tournamentId, $accountId, $rounds, $groupSize) {
    $POINTS_FOR_MATCH = 3;

    $in = "";
    $i = 0;

    foreach ($rounds as $item)
    {
        $key = ":id".$i++;
        $in .= "$key,";
        $in_params[$key] = $item; // collecting values into a key-value array
    }
    $in = rtrim($in,","); // :id0,:id1,:id2

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
                WHERE (tr.tournament_id = :tournament_id) AND (mr.result_confirmed = true) AND (tr.round_id IN (' . $in . '))
                
                UNION
                
                SELECT mr.match_id, m.player_id_2 AS player_id, m.player_id_1 AS opponent_id, mr.player_2_games_won AS player_games, mr.player_1_games_won AS opponent_games,
                    a.display_name AS display_name, mr.player_2_games_won > mr.player_1_games_won AS match_won
                FROM match_results AS mr
                LEFT JOIN matches m on mr.match_id = m.match_id
                LEFT JOIN accounts a on m.player_id_2 = a.account_id
                LEFT JOIN tournament_rounds tr on m.tournament_round_id  = tr.round_id
                WHERE (tr.tournament_id = :tournament_id) AND (mr.result_confirmed = true) AND (tr.round_id IN (' . $in . '))
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
    $values = array_merge($values, $in_params);

    return getRanking($accountId, $query, $values, $groupSize);
}

function getRanking($accountId, $query, $values, $groupSize) {
    $groupNames = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
    $ranking = array();

    $res = executeSQL($query, $values);

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
                $sumOpponentMP += max($opponentRank["MWPOriginal"], 1.0/3.0);
                $sumOpponentGP += max($opponentRank["GWPOriginal"], 1.0/3.0);
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
    $group = 0;
    $currentGroupSize = 0;

    foreach ($ranking as &$participant) {
        $participant["rank"] = $rank++;

        if ($currentGroupSize == $groupSize) {
            $group++;
            $currentGroupSize = 0;
        }

        $participant["group"] = $groupNames[$group];
        $currentGroupSize++;
    }

    return $ranking;
}

function getInitialRanking($tournamentId, $accountId, $groupSize) {
    $groupNames = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
    
    $query = 'SELECT tp.tournament_id, a.account_id AS player_id, a.display_name AS display_name, tp.initial_rank
            FROM tournament_participants AS tp
            INNER JOIN accounts AS a USING(account_id)
            WHERE (tournament_id = :tournament_id)
            ORDER BY tp.initial_rank';

    $values = [':tournament_id' => $tournamentId];

    $ranking = array();

    $res = executeSQL($query, $values);

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $ranking_item=array(
            "playerId" => $player_id,
            "displayName" => $display_name,
            "totalPoints" => 0,
            "matchesPlayed" => 0,
            "matchesWon" => 0,
            "gamesPlayed" => 0,
            "gamesWon" => 0,
            "MWPOriginal" => 0.0,
            "GWPOriginal" => 0.0,
            "MWP" => 0.0,
            "GWP" => 0.0,
            "opponents" => [],
            "you" => $player_id === $accountId
        );
    
        array_push($ranking, $ranking_item);
    }

    foreach ($ranking as &$participant) {
        $participant["OMP"] = 0.0;
        $participant["OGP"] = 0.0;
        $participant["OMPOriginal"] = 0.0;
        $participant["OGPOriginal"] = 0.0;
    }
    
    $rank = 1;
    $group = 0;
    $currentGroupSize = 0;

    foreach ($ranking as &$participant) {
        $participant["rank"] = $rank++;

        if ($currentGroupSize == $groupSize) {
            $group++;
            $currentGroupSize = 0;
        }

        $participant["group"] = $groupNames[$group];
        $currentGroupSize++;
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

?>
