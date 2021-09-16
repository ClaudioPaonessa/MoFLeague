<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/profileHelper.php';
require_once '../../helper/rankingHelper.php';

$accountId = getId();

$profileData = getProfile($accountId);

// TODO: Get last tournament ID and not use constant value (currently using 2 --> S03)
$lastRanking = getLiveRanking(2, $accountId, 1);

$profileData["stats"] = getPlayerStats($accountId, $lastRanking);

echo json_encode($profileData);

http_response_code(200);

?>