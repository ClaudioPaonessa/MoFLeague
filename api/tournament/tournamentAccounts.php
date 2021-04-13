<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../auth/checkAdmin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';

$tournamentId = getId();

$query = 'SELECT  a.account_id AS account_id, a.display_name AS display_name
            FROM accounts AS a
            WHERE NOT EXISTS (
            SELECT *
            FROM tournament_participants AS tp
            WHERE (tp.tournament_id = :tournament_id) AND (tp.account_id = a.account_id)
            )';

$values = [':tournament_id' => $tournamentId];

$res = executeSQL($query, $values);

$participants_arr = array();
$participants_arr["records"] = array();

while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
    extract($row);

    $participant_item=array(
        "accountId" => $account_id,
        "displayName" => $display_name
    );

    array_push($participants_arr["records"], $participant_item);
}

http_response_code(200);

echo json_encode($participants_arr);

?>