<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../auth/checkAdmin.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$tournamentName = $request->name;
$setId = $request->set;
$groupSize = $request->groupSize;
$matchesPerRound = $request->matchesPerRound;

$query = 'INSERT INTO tournaments (tournament_name, set_id, group_size, matches_per_round) VALUES (:tournament_name, :set_id, :group_size, :matches_per_round)';
$values = [':tournament_name' => $tournamentName, ':set_id' => $setId, ':group_size' => $groupSize, ':matches_per_round' => $matchesPerRound];

executeSQL($query, $values);

http_response_code(200);

?>