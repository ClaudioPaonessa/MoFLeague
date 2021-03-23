<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../auth/checkAdmin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';

$roundId = getId();

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$accountId1 = $request->accountId1;
$accountId2 = $request->accountId2;

$query = 'INSERT INTO matches (tournament_round_id, player_id_1, player_id_2) VALUES (:tournament_round_id, :player_id_1, :player_id_2)';
$values = [':tournament_round_id' => $roundId, ':player_id_1' => $accountId1, ':player_id_2' => $accountId2];

executeSQL($query, $values);

http_response_code(200);

?>
