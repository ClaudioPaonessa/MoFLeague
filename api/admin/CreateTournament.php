<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../auth/checkAdmin.php';
require_once '../../helper/errorHelper.php';
require_once '../../db/pdo.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$tournamentName = $request->name;
$setId = $request->set;

$query = 'INSERT INTO tournaments (tournament_name, set_id) VALUES (:tournament_name, :set_id)';
$values = [':tournament_name' => $tournamentName, ':set_id' => $setId];

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