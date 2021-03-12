<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../db/pdo.php';

$tournamentId = getId();

$tournamentData = array();
$tournamentData["currentMatches"] = array();
$tournamentData["rounds"] = array();
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

function getRounds($tournamentId, $currentRoundIndex, $pdo) {
    $query = 'SELECT tr.round_id, tr.date_start, tr.date_end
    FROM tournament_rounds AS tr
    WHERE (tournament_id = :tournament_id)
    ORDER BY tr.date_start ASC';

    $values = [':tournament_id' => $tournamentId];

    $rounds = array();

    try
    {
        $res = $pdo->prepare($query);
        $res->execute($values);
    }
    catch (PDOException $e)
    {
        returnError("Error in SQL query.");
    }

    $i = 1;

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $roundItem=array(
            "roundId" => $round_id,
            "name" => "Round " . $i++,
            "dateStart" =>  (new DateTime($date_start, new DateTimeZone("Europe/Zurich")))->format('Y-m-d'),
            "dateEnd" => (new DateTime($date_end, new DateTimeZone("Europe/Zurich")))->format('Y-m-d'),
            "active" => $currentRoundIndex === $round_id
        );

        array_push($rounds, $roundItem);
    }

    return $rounds;
}

function getCurrentRoundIndex($tournamentId, $pdo) {
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

function getNumberOfRounds($tournamentId, $pdo) {
    $query = 'SELECT COUNT(1) AS count
        FROM tournament_rounds AS tr
        WHERE (tr.tournament_id = :tournament_id)';

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

        return $count;
    }

    return 0;
}

function getCurrentRoundsFinished($tournamentId, $pdo) {
    $query = 'SELECT COUNT(1) AS count
        FROM tournament_rounds AS tr
        WHERE (tr.tournament_id = :tournament_id) AND (tr.date_start <= CURDATE()) AND (tr.date_end < CURDATE())';

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

        return $count;
    }

    return 0;
}

function getCurrentMatches($roundId, $pdo) {
    $matches = array();
    
    $query = 'SELECT m.match_id, m.player_id_1, m.player_id_2, 
        p1.mtg_arena_name AS p1_mtg_arena_name, p1.display_name AS p1_display_name, 
        p2.mtg_arena_name AS p2_mtg_arena_name, p2.display_name AS p2_display_name, 
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
            "p1MTGArenaName" => $p1_mtg_arena_name,
            "p1DisplayName" => $p1_display_name,
            "p2MTGArenaName" => $p2_mtg_arena_name,
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

$currentRoundIndex = getCurrentRoundIndex($tournamentId, $pdo);
$numberOfRounds = getNumberOfRounds($tournamentId, $pdo);
$roundsFinished = getCurrentRoundsFinished($tournamentId, $pdo);

$tournamentData["currentRoundId"] = $currentRoundIndex;
$tournamentData["numberOfRounds"] = intval($numberOfRounds);
$tournamentData["roundsFinished"] = intval($roundsFinished);
$tournamentData["rounds"] = getRounds($tournamentId, $currentRoundIndex, $pdo);

if ($currentRoundIndex >= 0) {
    $matches = getCurrentMatches($currentRoundIndex, $pdo);
    $tournamentData["currentMatches"] = $matches;
}

http_response_code(200);

echo json_encode($tournamentData);

?>