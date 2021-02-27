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
require '../../db/pdo.php';

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

// set response code - 200 OK
http_response_code(200);

function loadCards($pdo, $set_id, $url) {
    //sleep(0.5);

    $stmt = $pdo->prepare('INSERT INTO magic_cards (card_id_scryfall, magic_set_id, card_collector_number, card_name, card_type_line, card_image_uri, card_mana_cost, card_name_back, card_type_line_back, card_image_uri_back, card_mana_cost_back) VALUES(:card_id_scryfall, :magic_set_id, :card_collector_number, :card_name, :card_type_line, :card_image_uri, :card_mana_cost, :card_name_back, :card_type_line_back, :card_image_uri_back, :card_mana_cost_back)');

    $json_cards = file_get_contents($url);
    $cards = json_decode($json_cards);

    if ($cards === null) {
        return;
    }

    foreach ($cards->data as &$card) {
        try
        {
            $stmt->bindValue(':card_id_scryfall', $card->id);
            $stmt->bindValue(':card_collector_number', $card->collector_number);

            if (isset($card->card_faces)) {
                $card_face_front = $card->card_faces[0];
                $card_face_back = $card->card_faces[1];
                
                $stmt->bindValue(':magic_set_id', $set_id);
                
                $stmt->bindValue(':card_name', $card_face_front->name);
                $stmt->bindValue(':card_type_line', $card_face_front->type_line);
                $stmt->bindValue(':card_image_uri',$card_face_front->image_uris->normal);
                $stmt->bindValue(':card_mana_cost', $card_face_front->mana_cost);
                
                $stmt->bindValue(':card_name_back', $card_face_back->name);
                $stmt->bindValue(':card_type_line_back', $card_face_back->type_line);
                $stmt->bindValue(':card_image_uri_back',$card_face_back->image_uris->normal);
                $stmt->bindValue(':card_mana_cost_back', $card_face_back->mana_cost);

                
            }
            else {
                $card_face_front = $card;

                $stmt->bindValue(':magic_set_id', $set_id);
                $stmt->bindValue(':card_name', $card_face_front->name);
                $stmt->bindValue(':card_type_line', $card_face_front->type_line);
                $stmt->bindValue(':card_image_uri',$card_face_front->image_uris->normal);
                $stmt->bindValue(':card_mana_cost', $card_face_front->mana_cost);

                $stmt->bindValue(':card_name_back', "");
                $stmt->bindValue(':card_type_line_back', "");
                $stmt->bindValue(':card_image_uri_back',"");
                $stmt->bindValue(':card_mana_cost_back', "");
            }

            $stmt->execute();
        }
        catch (PDOException $e)
        {
            
        }
    }

    if (boolval($cards->has_more)) {
        if (isset($cards->next_page)) {
            loadCards($pdo, $set_id, $cards->next_page);
        }
    }
}

loadCards($pdo, 9, "https://api.scryfall.com/cards/search?order=set&q=e%3Akhm&unique=prints");

?>