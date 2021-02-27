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

function get_content($URL){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $URL);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

if (!isset($uri[4])) {
    header("HTTP/1.1 404 Not Found");
    exit();
}

$setId = (int) $uri[4];

// Include db config file
require '../../db/pdo.php';

function loadCards($pdo, $set_id, $url) {
    $stmt = $pdo->prepare('INSERT INTO magic_cards (card_id_scryfall, magic_set_id, card_collector_number, card_name, card_type_line, card_image_uri, card_mana_cost, card_name_back, card_type_line_back, card_image_uri_back, card_mana_cost_back) VALUES(:card_id_scryfall, :magic_set_id, :card_collector_number, :card_name, :card_type_line, :card_image_uri, :card_mana_cost, :card_name_back, :card_type_line_back, :card_image_uri_back, :card_mana_cost_back)');

    $json_cards = get_content($url);
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

// Prepare a select statement
$query = 'SELECT * FROM magic_sets WHERE (set_id = :set_id)';
$values = [':set_id' => $setId];

try
{
    $res = $pdo->prepare($query);
    $res->execute($values);
}
catch (PDOException $e)
{
    header("HTTP/1.1 404 Not Found");
    die();
}

$row = $res->fetch(PDO::FETCH_ASSOC);
$url = "";

if (is_array($row))
{
    $url = $row['scryfall_search_url'];
}

loadCards($pdo, $setId, $url);

// set response code - 200 OK
http_response_code(200);

?>