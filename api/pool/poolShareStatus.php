<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/poolHelper.php';

$tournamentId = getId();

$cardPool = array();
$cardPool["shareStatus"] = getShareStatus($tournamentId, $_SESSION["id"]);

echo json_encode($cardPool);

http_response_code(200);

?>