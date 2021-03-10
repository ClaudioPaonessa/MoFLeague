<?php

session_start();

require_once '../../auth/check_login.php';
require_once '../../auth/check_admin.php';
require_once '../../helper/url_id_helper.php';
require_once '../../db/pdo.php';

$roundId = get_id();

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$accountId1 = $request->accountId1;
$accountId2 = $request->accountId2;

$query = 'INSERT INTO matches (tournament_round_id, player_id_1, player_id_2) VALUES (:tournament_round_id, :player_id_1, :player_id_2)';
$values = [':tournament_round_id' => $roundId, ':player_id_1' => $accountId1, ':player_id_2' => $accountId2];

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
