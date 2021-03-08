<?php

// Initialize the session
session_start();

require_once '../../auth/check_login.php';
require_once '../../auth/check_admin.php';
require_once '../../helper/get_content_helper.php';
require_once '../../db/pdo.php';

$json_sets = get_content('https://api.scryfall.com/sets');
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

?>