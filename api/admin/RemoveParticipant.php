<?php

session_start();

require_once '../../auth/check_login.php';
require_once '../../auth/check_admin.php';
require_once '../../helper/url_id_helper.php';
require_once '../../db/pdo.php';

$tournamentId = get_id();
$accountId = get_id2();

$query = 'DELETE FROM tournament_participants WHERE (tournament_id = :tournament_id) AND (account_id = :account_id)';
$values = [':tournament_id' => $tournamentId, ':account_id' => $accountId];

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