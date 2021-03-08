<?php

session_start();

require_once '../../auth/check_login.php';
require_once '../../auth/check_admin.php';
require_once '../../db/pdo.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$tournament_name = $request->name;

$query = 'INSERT INTO tournaments (tournament_name) VALUES (:tournament_name)';
$values = [':tournament_name' => $tournament_name];

try
{
    $res = $pdo->prepare($query);
    $res->execute($values);
}
catch (PDOException $e)
{
    header("HTTP/1.1 404 Not Found");
    die();
}

http_response_code(200);

?>