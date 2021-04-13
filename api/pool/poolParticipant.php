<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/poolHelper.php';

$tournamentId = getId();

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$poolPinCode = $request->pin;
$accountId = $request->accountId;

$cardPool = array();
$cardPool["accountId"] = $accountId;

if (checkPin($tournamentId, $accountId, $poolPinCode)) {
    $cardPool["displayName"] = getDisplayName($accountId);
    $cardPool["initialPool"] = getInitialCardPool($tournamentId, $accountId);
    $cardPool["incomingTrades"] = getIncomingTrades($tournamentId, $accountId);
    $cardPool["outgoingTrades"] = getOutgoingTrades($tournamentId, $accountId);
    $cardPool["pool"] = getCurrentCardPool($cardPool["initialPool"], $cardPool["incomingTrades"], $cardPool["outgoingTrades"]);
} else {
    returnError("Pool is currently not shared or PIN is wrong.");
}

echo json_encode($cardPool);

http_response_code(200);

?>