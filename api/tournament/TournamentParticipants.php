<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../auth/checkAdmin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../db/pdo.php';

$tournamentId = getId();

$query = 'SELECT tp.tournament_id, a.account_id AS account_id, a.account_name AS account_name, a.display_name AS display_name
FROM tournament_participants AS tp
INNER JOIN accounts AS a USING(account_id)
WHERE (tournament_id = :tournament_id)';

$values = [':tournament_id' => $tournamentId];

$participants = array();
$participants["records"] = array();

try
{
    $res = $pdo->prepare($query);
    $res->execute($values);
}
catch (PDOException $e)
{
    returnError("Error in SQL query.");
}

while ($row = $res->fetch(PDO::FETCH_ASSOC)){
    extract($row);

    $participantItem=array(
        "accountId" => $account_id,
        "accountName" => $account_name,
        "displayName" => $display_name
    );

    array_push($participants["records"], $participantItem);
}

http_response_code(200);

echo json_encode($participants);

?>