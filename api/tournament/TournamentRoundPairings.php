<?php

session_start();

require_once '../../auth/check_login.php';
require_once '../../auth/check_admin.php';
require_once '../../helper/url_id_helper.php';
require_once '../../db/pdo.php';

$roundId = get_id();

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
    header("HTTP/1.1 404 Not Found");
    die();
}

$i = 1;

while ($row = $res->fetch(PDO::FETCH_ASSOC)){
    extract($row);

    $match_item=array(
        "match_id" => $match_id,
        "tournament_round_id" =>  $tournament_round_id,
        "player_id_1" => $player_id_1,
        "player_id_2" => $player_id_2,
        "display_name_1" => $display_name_1,
        "display_name_2" => $display_name_2
    );

    array_push($matches_arr["records"], $match_item);
}

http_response_code(200);

echo json_encode($matches_arr);

?>
