<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../db/pdo.php';

$tournamentId = getId();

$query = 'SELECT tr.round_id, tr.date_start, tr.date_end
FROM tournament_rounds AS tr
WHERE (tournament_id = :tournament_id)
ORDER BY tr.date_start ASC';

$values = [':tournament_id' => $tournamentId];

$rounds = array();
$rounds["records"] = array();

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

    $roundItem=array(
        "roundId" => $round_id,
        "name" => "Round " . $i++,
        "dateStart" =>  (new DateTime($date_start, new DateTimeZone("Europe/Zurich")))->format('Y-m-d'),
        "dateEnd" => (new DateTime($date_end, new DateTimeZone("Europe/Zurich")))->format('Y-m-d')
    );

    array_push($rounds["records"], $roundItem);
}

http_response_code(200);

echo json_encode($rounds);

?>