<?php

session_start();

require_once '../../auth/check_login.php';
require_once '../../helper/url_id_helper.php';
require_once '../../db/pdo.php';

$tournamentId = get_id();

$tournamentData = array();
$tournamentData["currentMatches"] = array();
$tournamentData["currentRoundId"] = -1;
$tournamentData["tournamentName"] = "";

function get_tournament_name($tournamentId, $pdo) {
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
        echo 'Query error.';
        die();
    }

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        return $tournament_name;
    }

    return "";
}

function get_current_round($tournamentId, $pdo) {
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
        echo 'Query error.';
        die();
    }

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        return $round_id;
    }

    return -1;
}

function get_current_matches($roundId, $pdo) {
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
        echo 'Query error.';
        die();
    }

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $match_item=array(
            "match_id" => $match_id,
            "player_id_1" => $player_id_1,
            "player_id_2" => $player_id_2,
            "p1_account_name" => $p1_account_name,
            "p1_display_name" => $p1_display_name,
            "p2_display_name" => $p2_display_name,
            "p2_display_name" => $p2_display_name,
            "player_1_games_won" => $player_1_games_won,
            "player_2_games_won" => $player_2_games_won,
            "result_confirmed" => boolval($result_confirmed),
            "reporter_you" => $reporter_account_id == $_SESSION["id"]
        );
    
        array_push($matches, $match_item);
    }

    return $matches;
}

$tournamentData["tournamentName"] = get_tournament_name($tournamentId, $pdo);

$current_round = get_current_round($tournamentId, $pdo);
$tournamentData["currentRoundId"] = $current_round;

if ($current_round >= 0) {
    $matches = get_current_matches($current_round, $pdo);
    $tournamentData["currentMatches"] = $matches;
}

http_response_code(200);

echo json_encode($tournamentData);

?>