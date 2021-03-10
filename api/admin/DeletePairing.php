<?php

session_start();

require_once '../../auth/check_login.php';
require_once '../../auth/check_admin.php';
require_once '../../helper/url_id_helper.php';
require_once '../../db/pdo.php';

$matchId = get_id();

$query = 'DELETE FROM matches WHERE (match_id = :matchId)';
$values = [':matchId' => $matchId];

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
