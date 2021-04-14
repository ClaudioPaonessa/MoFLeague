<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/tournamentHelper.php';
require_once '../../helper/poolHelper.php';
require_once '../../helper/packHelper.php';

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
    // You cannot trade stuff which is not yet transferred
    $cardPool["incomingTrades"] = getIncomingTrades($tournamentId, $accountId, TRUE);
    $cardPool["outgoingTrades"] = getOutgoingTrades($tournamentId, $accountId, FALSE);

    $rounds = getRounds($tournamentId, 0);
    $tournamentData["rounds"] = $rounds;
    $roundsKeyValuePair = getRoundsKeyValuePair($rounds);
    $cardPool["receivedCardPacks"] = getReceivedCardPacks($tournamentId, $_SESSION["id"], $roundsKeyValuePair);

    $cardPool["pool"] = getCurrentCardPool($cardPool["initialPool"], $cardPool["incomingTrades"], $cardPool["outgoingTrades"], $cardPool["receivedCardPacks"]);
} else {
    returnError("Pool is currently not shared or PIN is wrong.");
}

echo json_encode($cardPool);

http_response_code(200);

?>