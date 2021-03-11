<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../auth/checkAdmin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../db/pdo.php';

$roundId = getId();

$query = 'SELECT m.match_id, m.tournament_round_id, m.player_id_1, m.player_id_2, a1.display_name AS display_name_1, a2.display_name AS display_name_2
FROM matches AS m
INNER JOIN accounts AS a1 ON a1.account_id = m.player_id_1
INNER JOIN accounts AS a2 ON a2.account_id = m.player_id_2
WHERE (tournament_round_id = :roundId)
ORDER BY m.match_id ASC';

$values = [':roundId' => $roundId];

$matches_arr = array();
$matches_arr["records"] = array();

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

    $match_item=array(
        "matchId" => $match_id,
        "tournamentRoundId" =>  $tournament_round_id,
        "playerId1" => $player_id_1,
        "playerId2" => $player_id_2,
        "displayName1" => $display_name_1,
        "displayName2" => $display_name_2
    );

    array_push($matches_arr["records"], $match_item);
}

http_response_code(200);

echo json_encode($matches_arr);

?>
