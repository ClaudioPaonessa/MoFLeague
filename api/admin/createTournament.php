<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../auth/checkAdmin.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$tournamentName = $request->name;
$setId = $request->set;
$groupSize = $request->groupSize;

$query = 'INSERT INTO tournaments (tournament_name, set_id, group_size) VALUES (:tournament_name, :set_id, :group_size)';
$values = [':tournament_name' => $tournamentName, ':set_id' => $setId, ':group_size' => $groupSize];

executeSQL($query, $values);

http_response_code(200);

?>