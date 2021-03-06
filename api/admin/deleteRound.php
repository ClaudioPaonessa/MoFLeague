<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../auth/checkAdmin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';

$roundId = getId();

$query = 'DELETE FROM tournament_rounds WHERE (round_id = :round_id)';
$values = [':round_id' => $roundId];

executeSQL($query, $values);

http_response_code(200);

?>