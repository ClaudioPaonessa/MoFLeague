<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/tournamentHelper.php';

$tournamentId = getId();

$tournamentData = array();

$setId = getCardSetId($tournamentId);
$tournamentData["cards"] = getSetCards($setId);

http_response_code(200);

echo json_encode($tournamentData);

?>