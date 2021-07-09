<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/achievementHelper.php';

$tournamentId = getId();
$accountId = getId2();

$achievements = array();
$achievements["selectable"] = getSelectableAchievements($tournamentId, $accountId);

echo json_encode($achievements);

http_response_code(200);

?>