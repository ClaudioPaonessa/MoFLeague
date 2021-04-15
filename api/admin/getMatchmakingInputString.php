<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../auth/checkAdmin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/pairingsHelper.php';

$roundId = getId();

$pairingData = array();
$pairingData["yamlString"] = getMatchMakingInputString($roundId);

echo json_encode($pairingData);

http_response_code(200);

?>