<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../auth/checkAdmin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';

$tournamentId = getId();
$accountId = getId2();

$query = 'UPDATE tournament_participants SET payed = FALSE
        WHERE (tournament_id = :tournament_id) AND (account_id = :account_id)';
        
$values = [':tournament_id' => $tournamentId, ':account_id' => $accountId];

executeSQL($query, $values);

http_response_code(200);

?>