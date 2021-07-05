<?php

require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';

function getProfile($accountId) {
    $query = 'SELECT a.account_id, a.display_name, a.mtg_arena_name, a.admin_privilege
        FROM accounts AS a
        WHERE (a.account_id = :account_id)';

    $values = [':account_id' => $accountId];

    $res = executeSQL($query, $values);
    $row = $res->fetch(PDO::FETCH_ASSOC);
    
    if (is_array($row)) {
        extract($row);

        $profile=array(
            "accountId" => $account_id,
            "displayName" => $display_name,
            "mtgArenaName" => $mtg_arena_name,
            "adminPrivilege" => $admin_privilege
        );

        return $profile;
    }

    returnError("Profile not found");
}

function getPlayerStats($accountId, $lastRanking) {
    $query = 'SELECT m.match_id, m.player_id_1, m.player_id_2, 
        mr.player_1_games_won AS player_1_games_won, mr.player_2_games_won AS player_2_games_won,
        mr.result_confirmed AS result_confirmed
        FROM matches AS m
        LEFT JOIN accounts p1 on (m.player_id_1 = p1.account_id)
        LEFT JOIN accounts p2 on (m.player_id_2 = p2.account_id)
        LEFT JOIN match_results mr on (m.match_id = mr.match_id)
        WHERE ((m.player_id_1 = :player_id) OR (m.player_id_2 = :player_id)) AND (mr.result_confirmed = TRUE)
        GROUP BY m.match_id';

    $values = [':player_id' => $accountId];

    $res = executeSQL($query, $values);

    $matchesCount = 0;
    $matchesWonCount = 0;
    $gamesCount = 0;
    $gamesWonCount = 0;

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $matchesCount += 1;
        $gamesCount += ($player_1_games_won + $player_2_games_won);

        if ($player_id_1 == $accountId) {
            $gamesWonCount += $player_1_games_won;

            if ($player_1_games_won > $player_2_games_won) {
                $matchesWonCount += 1;
            }
            
        } else {
            $gamesWonCount += $player_2_games_won;

            if ($player_2_games_won > $player_1_games_won) {
                $matchesWonCount += 1;
            }
        }
    }

    $stats=array(
        "matchesCount" => $matchesCount,
        "matchesWonCount" => $matchesWonCount,
        "gamesCount" => $gamesCount,
        "gamesWonCount" => $gamesWonCount,
        "matchesWinPercentage" => round($matchesWonCount / $matchesCount * 100),
        "gamesWinPercentage" => round($gamesWonCount / $gamesCount * 100),
        "rank" => getRank($accountId, $lastRanking)
    );

    return $stats;
}

function getRank($accountId, $lastRanking) {
    $key = array_search($accountId, array_column($lastRanking, "playerId"));
    
    if (false !== $key)
    {
        if ($key == 0) {
            return "mythic";
        } else {
            $count = count($lastRanking);
            $maxRareRank = intval(($count-1) * 0.4);
            
            if ($key <= $maxRareRank) {
                return "rare";
            } else{
                return "uncommon";
            }
        }
    }

    return "common";
}

?>