<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../auth/checkAdmin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';

$tournamentId = getId();

$query = 'SELECT tp.tournament_id, a.account_id AS account_id
            FROM tournament_participants AS tp
            INNER JOIN accounts AS a USING(account_id)
            WHERE (tournament_id = :tournament_id)
            ORDER BY RAND()';

$values = [':tournament_id' => $tournamentId];

$res = executeSQL($query, $values);

$rank = 1;

while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
    extract($row);

    $query = 'UPDATE tournament_participants
                SET initial_rank = :initial_rank
                WHERE (tournament_id = :tournament_id) AND (account_id = :account_id)';
    
    $values = [':tournament_id' => $tournament_id, ':account_id' => $account_id, ':initial_rank' => $rank++];

    executeSQL($query, $values);
}

http_response_code(200);

?>