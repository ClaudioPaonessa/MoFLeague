<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../auth/checkAdmin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../db/pdo.php';

$tournamentId = getId();

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$roundId = intval($request->roundId);

$query = 'UPDATE tournaments SET active_round_id = :round_id WHERE (tournament_id = :tournament_id)';
$values = [':tournament_id' => $tournamentId, ':round_id' => $roundId];

try
{
    $res = $pdo->prepare($query);
    $res->execute($values);
}
catch (PDOException $e)
{
    returnError("Error in SQL query.");
}

?>