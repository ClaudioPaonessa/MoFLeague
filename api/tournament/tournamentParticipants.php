<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../auth/checkAdmin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';

$tournamentId = getId();

$query = 'SELECT tp.tournament_id, a.account_id AS account_id, a.display_name AS display_name, tp.initial_rank, tp.payed
            FROM tournament_participants AS tp
            INNER JOIN accounts AS a USING(account_id)
            WHERE (tournament_id = :tournament_id)
            ORDER BY tp.initial_rank';

$values = [':tournament_id' => $tournamentId];

$participants = array();
$participants["records"] = array();

$res = executeSQL($query, $values);

while ($row = $res->fetch(PDO::FETCH_ASSOC)){
    extract($row);

    $participantItem=array(
        "tournamentId" => $tournament_id,
        "accountId" => $account_id,
        "displayName" => $display_name,
        "initialRank" => $initial_rank,
        "payed" => boolval($payed)
    );

    array_push($participants["records"], $participantItem);
}

echo json_encode($participants);

http_response_code(200);

?>