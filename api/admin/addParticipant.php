<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../auth/checkAdmin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';

$tournamentId = getId();
$accountId = getId2();

$query = 'INSERT INTO tournament_participants (tournament_id, account_id) VALUES (:tournament_id, :account_id)';
$values = [':tournament_id' => $tournamentId, ':account_id' => $accountId];

executeSQL($query, $values);

http_response_code(200);

?>