<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../auth/checkAdmin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../db/pdo.php';

$tournamentId = getId();
$accountId = getId2();

$query = 'DELETE FROM tournament_participants WHERE (tournament_id = :tournament_id) AND (account_id = :account_id)';
$values = [':tournament_id' => $tournamentId, ':account_id' => $accountId];

try
{
    $res = $pdo->prepare($query);
    $res->execute($values);
}
catch (PDOException $e)
{
    returnError("Error in SQL query.");
}

http_response_code(200);

?>