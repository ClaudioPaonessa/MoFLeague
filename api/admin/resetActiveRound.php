<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../auth/checkAdmin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';

$tournamentId = getId();

$query = 'UPDATE tournaments SET active_round_id = NULL WHERE (tournament_id = :tournament_id)';
$values = [':tournament_id' => $tournamentId];

executeSQL($query, $values);

http_response_code(200);

?>