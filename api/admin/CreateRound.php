<?php

session_start();

require_once '../../auth/check_login.php';
require_once '../../auth/check_admin.php';
require_once '../../helper/url_id_helper.php';
require_once '../../db/pdo.php';

$tournamentId = get_id();

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$date_start = new DateTime($request->date_start, new DateTimeZone("UTC"));
$date_end = new DateTime($request->date_end, new DateTimeZone("UTC"));

$date_start->setTimeZone(new DateTimeZone('Europe/Zurich'));
$date_end->setTimeZone(new DateTimeZone('Europe/Zurich'));

$query = 'INSERT INTO tournament_rounds (tournament_id, date_start, date_end) VALUES (:tournament_id, :date_start, :date_end)';
$values = [':tournament_id' => $tournamentId, ':date_start' => $date_start->format('Y-m-d'), ':date_end' => $date_end->format('Y-m-d')];

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

http_response_code(200);

?>