<?php

session_start();

require_once '../../auth/check_login.php';
require_once '../../auth/check_admin.php';
require_once '../../helper/url_id_helper.php';
require_once '../../db/pdo.php';

$tournamentId = get_id();

$query = 'SELECT tr.round_id, tr.date_start, tr.date_end
FROM tournament_rounds AS tr
WHERE (tournament_id = :tournament_id)
ORDER BY tr.date_start ASC';

$values = [':tournament_id' => $tournamentId];

$rounds_arr = array();
$rounds_arr["records"] = array();

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

    $round_item=array(
        "round_id" => $round_id,
        "name" => "Round " . $i++,
        "date_start" => $date_start,
        "date_end" => $date_end
    );

    array_push($rounds_arr["records"], $round_item);
}

http_response_code(200);

echo json_encode($rounds_arr);

?>