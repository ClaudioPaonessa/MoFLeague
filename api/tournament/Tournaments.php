<?php

session_start();

require_once '../../auth/check_login.php';
require_once '../../db/pdo.php';

$tournaments_arr = array();
$tournaments_arr["records"] = array();

$query = 'SELECT t.tournament_id, t.tournament_name, COUNT(p.account_id) AS participant_count
FROM tournaments AS t
LEFT JOIN tournament_participants AS p ON t.tournament_id = p.tournament_id
GROUP BY t.tournament_id';

try
{
    $res = $pdo->prepare($query);
    $res->execute();
}
catch (PDOException $e)
{
    echo 'Query error.';
    die();
}

while ($row = $res->fetch(PDO::FETCH_ASSOC)){
    extract($row);

    $tournament_item=array(
        "tournament_id" => $tournament_id,
        "tournament_name" => $tournament_name,
        "participant_count" => $participant_count
    );

    array_push($tournaments_arr["records"], $tournament_item);
}

http_response_code(200);

echo json_encode($tournaments_arr);

?>