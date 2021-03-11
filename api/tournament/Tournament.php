<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../db/pdo.php';

$tournamentId = getId();

$tournamentData = array();
$tournamentData["currentMatches"] = array();
$tournamentData["currentRoundId"] = -1;
$tournamentData["tournamentName"] = "";

function getTournamentName($tournamentId, $pdo) {
    $query = 'SELECT t.tournament_name
        FROM tournaments AS t
        WHERE (t.tournament_id = :tournament_id)';

    $values = [':tournament_id' => $tournamentId];

    try
    {
        $res = $pdo->prepare($query);
        $res->execute($values);
    }
    catch (PDOException $e)
    {
        returnError("Error in SQL query.");
    }

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        return $tournament_name;
    }

    return "";
}

function getCurrentRound($tournamentId, $pdo) {
    $query = 'SELECT tr.round_id
        FROM tournament_rounds AS tr
        WHERE (tr.tournament_id = :tournament_id) AND (tr.date_start <= CURDATE()) AND (tr.date_end >= CURDATE())';

    $values = [':tournament_id' => $tournamentId];

    try
    {
        $res = $pdo->prepare($query);
        $res->execute($values);
    }
    catch (PDOException $e)
    {
        returnError("Error in SQL query.");
    }

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        return $round_id;
    }

    return -1;
}

function getCurrentMatches($roundId, $pdo) {
    $matches = array();
    
    $query = 'SELECT m.match_id, m.player_id_1, m.player_id_2, 
        p1.account_name AS p1_account_name, p1.display_name AS p1_display_name, 
        p2.account_name AS p2_account_name, p2.display_name AS p2_display_name, 
        mr.player_1_games_won AS player_1_games_won, mr.player_2_games_won AS player_2_games_won,
        mr.result_confirmed AS result_confirmed, mr.reporter_account_id AS reporter_account_id
        FROM matches AS m
        LEFT JOIN accounts p1 on (m.player_id_1 = p1.account_id)
        LEFT JOIN accounts p2 on (m.player_id_2 = p2.account_id)
        LEFT JOIN match_results mr on (m.match_id = mr.match_id)
        WHERE (m.tournament_round_id = :tournament_round_id) AND ((m.player_id_1 = :player_id) OR (m.player_id_2 = :player_id))';

    $values = [':tournament_round_id' => $roundId, ':player_id' => $_SESSION["id"]];

    try
    {
        $res = $pdo->prepare($query);
        $res->execute($values);
    }
    catch (PDOException $e)
    {
        returnError("Error in SQL query.");
    }

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $match_item=array(
            "matchId" => $match_id,
            "playerId1" => $player_id_1,
            "playerId2" => $player_id_2,
            "p1AccountName" => $p1_account_name,
            "p1DisplayName" => $p1_display_name,
            "p2AccountName" => $p2_account_name,
            "p2DisplayName" => $p2_display_name,
            "player1GamesWon" => $player_1_games_won,
            "player2GamesWon" => $player_2_games_won,
            "resultConfirmed" => boolval($result_confirmed),
            "reporterYou" => $reporter_account_id == $_SESSION["id"]
        );
    
        array_push($matches, $match_item);
    }

    return $matches;
}

$tournamentData["tournamentName"] = getTournamentName($tournamentId, $pdo);

$currentRound = getCurrentRound($tournamentId, $pdo);
$tournamentData["currentRoundId"] = $currentRound;

if ($currentRound >= 0) {
    $matches = getCurrentMatches($currentRound, $pdo);
    $tournamentData["currentMatches"] = $matches;
}

http_response_code(200);

echo json_encode($tournamentData);

?>