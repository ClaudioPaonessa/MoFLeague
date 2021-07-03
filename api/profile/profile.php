<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/profileHelper.php';

$accountId = getId();

$profileData = getProfile($accountId);
$profileData["stats"] = getPlayerStats($accountId);

echo json_encode($profileData);

http_response_code(200);

?>