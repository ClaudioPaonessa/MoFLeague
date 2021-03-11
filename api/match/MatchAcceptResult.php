<?php

session_start();

require_once '../../auth/check_login.php';
require_once '../../helper/url_id_helper.php';
require_once '../../helper/match_helper.php';
require_once '../../db/pdo.php';

$matchId = get_id();

if (!checkIfAllowed($matchId, $_SESSION["id"], $pdo)) {
    echo 'Not allowed to accept this match result.';
    header("HTTP/1.1 404 Not Found");
    die();
}

if (checkIfReporter($matchId, $_SESSION["id"], $pdo)) {
    echo 'Not allowed to accept own reported match result.';
    header("HTTP/1.1 404 Not Found");
    die();
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
    header("HTTP/1.1 404 Not Found");
    die();
}

http_response_code(200);

?>
