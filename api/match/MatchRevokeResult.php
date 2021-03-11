<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/matchHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../db/pdo.php';

$matchId = getId();

if (!checkIfAllowed($matchId, $_SESSION["id"], $pdo)) {
    returnError("Not allowed to revoke this match result.");
}

$query = 'DELETE FROM match_results WHERE (match_id = :match_id)';
$values = [':match_id' => $matchId];

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
