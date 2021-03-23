<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../auth/checkAdmin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';

$matchId = getId();

$query = 'DELETE FROM matches WHERE (match_id = :matchId)';
$values = [':matchId' => $matchId];

executeSQL($query, $values);

http_response_code(200);

?>
