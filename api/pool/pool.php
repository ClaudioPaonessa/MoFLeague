<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/tournamentHelper.php';
require_once '../../helper/poolHelper.php';
require_once '../../helper/packHelper.php';

$tournamentId = getId();

$cardPool = array();

$cardPool["initialPool"] = getInitialCardPool($tournamentId, $_SESSION["id"]);
$cardPool["incomingTrades"] = getIncomingTrades($tournamentId, $_SESSION["id"], TRUE);
$cardPool["outgoingTrades"] = getOutgoingTrades($tournamentId, $_SESSION["id"], TRUE);
$cardPool["incomingTradesPlanned"] = getIncomingTradesPlanned($tournamentId, $_SESSION["id"]);
$cardPool["outgoingTradesPlanned"] = getOutgoingTradesPlanned($tournamentId, $_SESSION["id"]);

$rounds = getRounds($tournamentId, 0);
$tournamentData["rounds"] = $rounds;
$roundsKeyValuePair = getRoundsKeyValuePair($rounds);
$cardPool["receivedCardPacks"] = getReceivedCardPacks($tournamentId, $_SESSION["id"], $roundsKeyValuePair);

$cardPool["pool"] = getCurrentCardPool($cardPool["initialPool"], $cardPool["incomingTrades"], $cardPool["outgoingTrades"], $cardPool["receivedCardPacks"]);



$cardPool["shareStatus"] = getShareStatus($tournamentId, $_SESSION["id"]);

echo json_encode($cardPool);

http_response_code(200);

?>