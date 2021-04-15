<?php

require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';
require_once '../../helper/rankingHelper.php';
require_once '../../helper/tournamentHelper.php';

function getMatchMakingInputString($roundId) {
    $tournamentId = getTournamentFromRound($roundId);
    
    $groupSize = getTournamentGroupSize($tournamentId);
    $matchesPerRound = getTournamentMatchesPerRound($tournamentId);

    $matchMakingString = "group_size: " . $groupSize . "\n";
    $matchMakingString .= "matches_per_round: " . $matchesPerRound . "\n";
    $matchMakingString .= "already_played:" . "\n";

    $roundDateStart = getRoundStartDate($roundId);
    $rounds = getPreviousCompletedRounds($tournamentId, $roundDateStart);

    if (count($rounds) > 0) {
        $ranking = getRankingFromRounds($tournamentId, 0, $rounds, $groupSize);

        $in = "";
        $i = 0;

        foreach ($rounds as $item)
        {
            $key = ":id".$i++;
            $in .= "$key,";
            $in_params[$key] = $item; // collecting values into a key-value array
        }
        $in = rtrim($in,","); // :id0,:id1,:id2

        $query = 'SELECT m.player_id_1 AS player_id, m.player_id_2 AS opponent_id
                FROM match_results AS mr
                LEFT JOIN matches m on mr.match_id = m.match_id
                LEFT JOIN accounts a on m.player_id_1 = a.account_id
                LEFT JOIN tournament_rounds tr on m.tournament_round_id  = tr.round_id
                WHERE (tr.tournament_id = :tournament_id) AND (mr.result_confirmed = true) AND (tr.round_id IN (' . $in . '))';
        
        $values = [':tournament_id' => $tournamentId];
        $values = array_merge($values, $in_params);

        $res = executeSQL($query, $values);

        while ($row = $res->fetch(PDO::FETCH_ASSOC)){
            extract($row);

            $matchMakingString .= "  - " . $player_id . ";" . $opponent_id . "\n";
        }
    } else{
        $ranking = getInitialRanking($tournamentId, 0, $groupSize);
    }

    $matchMakingString .= "ranking:\n";

    foreach ($ranking as &$participant) {
        $matchMakingString .= "  - " . $participant["playerId"] . "\n";
    }

    return $matchMakingString;
}

function addPairings($roundId, $pairingsString) {
    $matches = explode("\n", $pairingsString);

    foreach ($matches as &$match) {
        $playerIds = explode(";", $match);

        addPairing($roundId, intval($playerIds[0]), intval($playerIds[1]));
    }
}

function addPairing($roundId, $accountId1, $accountId2) {
    $query = 'INSERT INTO matches (tournament_round_id, player_id_1, player_id_2) VALUES (:tournament_round_id, :player_id_1, :player_id_2)';
    $values = [':tournament_round_id' => $roundId, ':player_id_1' => $accountId1, ':player_id_2' => $accountId2];

    executeSQL($query, $values);
}

?>