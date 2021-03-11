<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/matchHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../db/pdo.php';

$matchId = getId();

if (!checkIfAllowed($matchId, $_SESSION["id"], $pdo)) {
    returnError("Not allowed to accept this match result.");
}

if (checkIfReporter($matchId, $_SESSION["id"], $pdo)) {
    returnError("Not allowed to accept own reported match result.");
}

$query = 'UPDATE match_results SET result_confirmed = :confirmed WHERE (match_id = :match_id)';
$values = [':match_id' => $matchId, ':confirmed' => TRUE];

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
