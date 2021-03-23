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
$status = boolval($request->status);

$query = 'UPDATE tournament_rounds SET completed = :completed WHERE (round_id = :round_id)';
$values = [':round_id' => $roundId, ':completed' => $status];

executeSQL($query, $values);

http_response_code(200);

?>