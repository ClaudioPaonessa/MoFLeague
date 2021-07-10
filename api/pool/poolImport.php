<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/urlIdHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/poolHelper.php';

$tournamentId = getId();

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
$cardPoolString = $request->cardPoolString;

$lines = preg_split('/\n|\r\n?/', $cardPoolString);

$poolImportInfo = array();
$poolImportInfo["importErrors"] = "";

$added = 0;


$setId = getTournamentSetId($tournamentId);
$errors = FALSE;
$cardIds = array();

foreach ( $lines as $line ) {
    $exploded = explode(" ", $line, 2);

    if (count($exploded) > 1) {
        $cardCount = $exploded[0];
        $splittedCard = explode(" (", $exploded[1], 2);
        $cardName = $splittedCard[0];
        
        for ($i = 0; $i < $cardCount; $i++) {
            $cardId = getCardId($cardName, $setId);
            
            if ($cardId != -1) {
                if ($cardId > 0) {
                    array_push($cardIds, $cardId);
                } else {
                    $poolImportInfo["importErrors"] .= "failed to add " . $cardName . "; ";
                    $errors = TRUE;
                }
            }
        }
    }
}

if (!$errors) {
    resetCardPool($tournamentId, $_SESSION["id"]);

    foreach ($cardIds as $cardId) {
        $added++;
        addCardToPool($tournamentId, $_SESSION["id"], $cardId);
    }
}

$poolImportInfo["message"] = "Successfully added " . $added . " cards to your pool.";

echo json_encode($poolImportInfo);

http_response_code(200);

?>