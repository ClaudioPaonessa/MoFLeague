<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../auth/checkAdmin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/packHelper.php';

$roundId = getId();

$packsData = array();
$packsData["participantPacks"] = getRoundPacks($roundId);

echo json_encode($packsData);

http_response_code(200);

?>