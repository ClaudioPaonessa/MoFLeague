<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/historyHelper.php';

$historyData = array();

$historyData["matches"] = getMatchHistoryForPlayer($_SESSION["id"]);

echo json_encode($historyData);

http_response_code(200);

?>