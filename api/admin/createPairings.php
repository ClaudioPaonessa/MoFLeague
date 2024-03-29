<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../auth/checkAdmin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';
require_once '../../helper/pairingsHelper.php';

$roundId = getId();

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$pairingsString = $request->pairingsString;

addPairings($roundId, $pairingsString);

http_response_code(200);

?>
