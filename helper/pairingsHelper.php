<?php

require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';
require_once '../../helper/rankingHelper.php';
require_once '../../helper/tournamentHelper.php';

function generatePairings($tournamentId, $roundId) {
    $groupSize = getTournamentGroupSize($tournamentId);
    $roundDateStart = getRoundStartDate($roundId);
    $previousCompletedRounds = getPreviousCompletedRounds($tournamentId, $roundDateStart);
    
    $completedRanking = getRankingFromRounds($tournamentId, 0, $previousCompletedRounds, $groupSize);

    $groupedPlayerIds = array();

    foreach ($completedRanking as $participant) {
        $groupedPlayerIds[$participant['group']][] = intval($participant['playerId']);
    }

    $groupMatches = array();
    $possibleRandomMatches = array();
    
    foreach ($groupedPlayerIds as $group) {
        foreach ($group as $keyP1=>$playerId1) {
            foreach ($group as $keyP2=>$playerId2) {
                if ($keyP1 < $keyP2) {
                    $match=array(
                        "p1" => $playerId1,
                        "p2" => $playerId2
                    );
                    
                    array_push($groupMatches, $match);
                }
            }
        }
    }

    foreach ($groupedPlayerIds as $keyG1=>$group1) {
        foreach ($groupedPlayerIds as $keyG2=>$group2) {
            if ($keyG1 < $keyG2) {
                foreach ($group1 as $playerId1) {
                    foreach ($group2 as $playerId2) {
                        $match=array(
                            "p1" => $playerId1,
                            "p2" => $playerId2
                        );
                        
                        array_push($possibleRandomMatches, $match);
                    }
                }
            }
        }
    }

    highlight_string("<?php\n\$data =\n" . var_export($groupedPlayerIds, true) . ";\n?>");
    echo "<br><br>";
    highlight_string("<?php\n\$data =\n" . var_export($groupMatches, true) . ";\n?>");
    echo "<br><br>";
    highlight_string("<?php\n\$data =\n" . var_export($possibleRandomMatches, true) . ";\n?>");
}

function addPairing($roundId, $accountId1, $accountId2) {
    $query = 'INSERT INTO matches (tournament_round_id, player_id_1, player_id_2) VALUES (:tournament_round_id, :player_id_1, :player_id_2)';
    $values = [':tournament_round_id' => $roundId, ':player_id_1' => $accountId1, ':player_id_2' => $accountId2];

    executeSQL($query, $values);
}

?>