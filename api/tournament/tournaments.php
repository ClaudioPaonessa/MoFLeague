<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/errorHelper.php';
require_once '../../db/pdo.php';

$tournaments = array();
$tournaments["records"] = array();

$query = 'SELECT t.tournament_id, t.tournament_name, COUNT(DISTINCT p.account_id) AS participant_count, COUNT(DISTINCT you.account_id) AS you_participate, s.set_name AS set_name, COUNT(DISTINCT tr.round_id) AS round_count, MIN(tr.date_start) AS start_date, MAX(tr.date_end) AS end_date
FROM tournaments AS t
INNER JOIN magic_sets AS s USING(set_id)
LEFT JOIN tournament_participants AS p ON t.tournament_id = p.tournament_id
LEFT JOIN tournament_participants AS you ON (:player_id = you.account_id) AND (t.tournament_id = you.tournament_id)
LEFT JOIN tournament_rounds AS tr ON t.tournament_id = tr.tournament_id
GROUP BY t.tournament_id
ORDER BY start_date DESC';

$values = [':player_id' => $_SESSION["id"]];

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

    $tournamentItem=array(
        "tournamentId" => $tournament_id,
        "tournamentName" => $tournament_name,
        "setName" => $set_name,
        "participantCount" => $participant_count,
        "roundCount" => $round_count,
        "startDate" => $start_date,
        "endDate" => $end_date,
        "youParticipate" => boolval($you_participate)
    );

    array_push($tournaments["records"], $tournamentItem);
}

http_response_code(200);

echo json_encode($tournaments);

?>