<?php

session_start();

require_once '../../auth/check_login.php';
require_once '../../auth/check_admin.php';
require_once '../../helper/url_id_helper.php';
require_once '../../db/pdo.php';

$tournamentId = get_id();

$query = 'SELECT tp.tournament_id, a.account_id AS account_id, a.account_name AS account_name, a.display_name AS display_name
FROM tournament_participants AS tp
INNER JOIN accounts AS a USING(account_id)
WHERE (tournament_id = :tournament_id)';

$values = [':tournament_id' => $tournamentId];

$participants_arr = array();
$participants_arr["records"] = array();

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

while ($row = $res->fetch(PDO::FETCH_ASSOC)){
    extract($row);

    $participant_item=array(
        "account_id" => $account_id,
        "account_name" => $account_name,
        "display_name" => $display_name
    );

    array_push($participants_arr["records"], $participant_item);
}

http_response_code(200);

echo json_encode($participants_arr);

?>