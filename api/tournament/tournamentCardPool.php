<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/tournamentHelper.php';
require_once '../../db/pdo.php';

$tournamentId = getId();

$tournamentData = array();

$setId = getCardSetId($tournamentId, $pdo);
$tournamentData["cards"] = getSetCards($setId, $pdo);

http_response_code(200);

echo json_encode($tournamentData);

?>