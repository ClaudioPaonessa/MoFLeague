<?php

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /auth/login.php");
    exit;
}

if (!isset($_SESSION["admin"]) || boolval($_SESSION["admin"]) !== true) {
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

// Include db config file
require '../db/pdo.php';

$json_sets = file_get_contents('https://api.scryfall.com/sets');
$sets = json_decode($json_sets);

$stmt = $pdo->prepare('INSERT INTO magic_sets (set_code, set_name, scryfall_search_url, release_date, set_type, set_icon_svg_uri) VALUES(:set_code, :set_name, :scryfall_search_url, :release_date, :set_type, :set_icon_svg_uri)');

foreach ($sets->data as &$set) {
    try
    {
        $stmt->bindValue(':set_code', $set->code);
        $stmt->bindValue(':set_name',$set->name);
        $stmt->bindValue(':scryfall_search_url',$set->search_uri);
        $stmt->bindValue(':release_date',$set->released_at);
        $stmt->bindValue(':set_type',$set->set_type);
        $stmt->bindValue(':set_icon_svg_uri',$set->icon_svg_uri);
        $stmt->execute();
    }
    catch (PDOException $e)
    {

    }
}

function loadCards($pdo, $url) {
    //sleep(0.5);

    $stmt = $pdo->prepare('INSERT INTO magic_cards (card_id_scryfall, card_name, card_image_uri, card_mana_cost) VALUES(:card_id_scryfall, :card_name, :card_image_uri, :card_mana_cost)');

    $json_cards = file_get_contents($url);
    $cards = json_decode($json_cards);

    if ($cards === null) {
        return;
    }

    foreach ($cards->data as &$card) {
        try
        {
            $stmt->bindValue(':card_id_scryfall', $card->id);
            $stmt->bindValue(':card_name',$card->name);
            $stmt->bindValue(':card_image_uri',$card->released_at);

            if (isset($card->mana_cost)) {
                $stmt->bindValue(':card_mana_cost', $card->mana_cost);
            }
            else{
                $stmt->bindValue(':card_mana_cost', "");
            }

            $stmt->execute();
        }
        catch (PDOException $e)
        {
            
        }
    }

    if (boolval($cards->has_more)) {
        if (isset($cards->next_page)) {
            loadCards($pdo, $cards->next_page);
        }
    }
}

loadCards($pdo, "https://api.scryfall.com/cards/search?order=set&q=e%3Akhm&unique=prints");

?>