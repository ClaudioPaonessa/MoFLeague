<?php

require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';

function getReceivedAchievements($tournamentId, $accountId) {
    $query = 'SELECT ac.achievement_id, ac.name, ac.description, ac.difficulty, ad.name as difficulty_name, ad.points
    FROM achievements_receivers AS ar
    LEFT JOIN match_results mr on (ar.match_id = mr.match_id)
    LEFT JOIN matches m on (mr.match_id = m.match_id)
    LEFT JOIN tournament_rounds tr on (m.tournament_round_id = tr.round_id)
    LEFT JOIN achievements ac on (ar.achievement_id = ac.achievement_id)
    LEFT JOIN achievements_difficulties ad on (ac.difficulty = ad.achievements_difficultiy_id )
    WHERE (ar.account_id = :account_id) AND (tr.tournament_id = :tournament_id) AND (mr.result_confirmed = TRUE)';

    $values = [':account_id' => $accountId, ':tournament_id' => $tournamentId];

    $res = executeSQL($query, $values);

    $receivedAchievements = array();

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $achievementItem=array(
            "achievementId" => $achievement_id,
            "name" => $name,
            "description" => $description,
            "difficultyName" => $difficulty_name,
            "points" => $points
        );

        array_push($receivedAchievements, $achievementItem);
    }

    return $receivedAchievements;
}

function getReceivedAchievementsPoints($tournamentId, $accountId) {
    $query = 'SELECT SUM(ad.points) as sum_points
    FROM achievements_receivers AS ar
    LEFT JOIN match_results mr on (ar.match_id = mr.match_id)
    LEFT JOIN matches m on (mr.match_id = m.match_id)
    LEFT JOIN tournament_rounds tr on (m.tournament_round_id = tr.round_id)
    LEFT JOIN achievements ac on (ar.achievement_id = ac.achievement_id)
    LEFT JOIN achievements_difficulties ad on (ac.difficulty = ad.achievements_difficultiy_id )
    WHERE (ar.account_id = :account_id) AND (tr.tournament_id = :tournament_id) AND (mr.result_confirmed = TRUE)';

    $values = [':account_id' => $accountId, ':tournament_id' => $tournamentId];

    $res = executeSQL($query, $values);
    $row = $res->fetch(PDO::FETCH_ASSOC);

    if (is_array($row)) {
        extract($row);
        return $sum_points;
    }
    
    return 0;
}

?>