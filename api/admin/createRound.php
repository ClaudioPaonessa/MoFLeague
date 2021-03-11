<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../auth/checkAdmin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../db/pdo.php';

$tournamentId = getId();

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$dateStart = new DateTime($request->date_start, new DateTimeZone("UTC"));
$dateEnd = new DateTime($request->date_end, new DateTimeZone("UTC"));

$date_start->setTimeZone(new DateTimeZone('Europe/Zurich'));
$date_end->setTimeZone(new DateTimeZone('Europe/Zurich'));

$query = 'INSERT INTO tournament_rounds (tournament_id, date_start, date_end) VALUES (:tournament_id, :date_start, :date_end)';
$values = [':tournament_id' => $tournamentId, ':date_start' => $dateStart->format('Y-m-d'), ':date_end' => $dateEnd->format('Y-m-d')];

try
{
    $res = $pdo->prepare($query);
    $res->execute($values);
}
catch (PDOException $e)
{
    returnError("Error in SQL query.");
}

http_response_code(200);

?>
