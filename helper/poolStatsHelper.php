<?php

require_once '../../helper/errorHelper.php';
require_once '../../helper/tournamentHelper.php';
require_once '../../helper/poolHelper.php';
require_once '../../helper/packHelper.php';
require_once '../../helper/dbHelper.php';

function getTournamentCardPoolStats($tournamentId) {
    $query = 'SELECT tp.tournament_id, a.account_id AS account_id, a.display_name AS display_name, tp.initial_rank, tp.payed
            FROM tournament_participants AS tp
            INNER JOIN accounts AS a USING(account_id)
            WHERE (tournament_id = :tournament_id)
            ORDER BY tp.initial_rank';

    $values = [':tournament_id' => $tournamentId];

    $participants = array();

    $res = executeSQL($query, $values);

    while ($row = $res->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        $cardPool = array();

        $initialPool = getInitialCardPool($tournamentId, $account_id);
        $incomingTrades = getIncomingTrades($tournamentId, $account_id, TRUE);
        $outgoingTrades = getOutgoingTrades($tournamentId, $account_id, TRUE);

        $rounds = getRounds($tournamentId, 0);
        $tournamentData["rounds"] = $rounds;
        $roundsKeyValuePair = getRoundsKeyValuePair($rounds);
        $receivedCardPacks = getReceivedCardPacks($tournamentId, $account_id, $roundsKeyValuePair);

        $cardPool = getCurrentCardPool($initialPool, $incomingTrades, $outgoingTrades, $receivedCardPacks);
        $commonCount = 0;
        $uncommonCount = 0;
        $rareCount = 0;
        $mythicCount = 0;

        foreach ($cardPool as &$card) {
            switch ($card["cardRarity"]) {
                case "common":
                    $commonCount += $card["numberOfCards"];
                    break;
                case "uncommon":
                    $uncommonCount += $card["numberOfCards"];
                    break;
                case "rare":
                    $rareCount += $card["numberOfCards"];
                    break;
                case "mythic":
                    $mythicCount += $card["numberOfCards"];
                    break;
            }
        }

        $participantItem=array(
            "accountId" => $account_id,
            "displayName" => $display_name,
            "commonCount" => $commonCount,
            "uncommonCount" => $uncommonCount,
            "rareCount" => $rareCount,
            "mythicCount" => $mythicCount
        );

        array_push($participants, $participantItem);
    }

    return $participants;
}

?>