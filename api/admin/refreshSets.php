<?php

// Initialize the session
session_start();

require_once '../../auth/checkLogin.php';
require_once '../../auth/checkAdmin.php';
require_once '../../helper/getContentHelper.php';
require_once '../../helper/errorHelper.php';
require_once '../../db/pdo.php';

$jsonSets = getContent('https://api.scryfall.com/sets');
$sets = json_decode($jsonSets);

$stmt = $pdo->prepare('INSERT INTO magic_sets (set_code, set_name, scryfall_search_url, release_date, set_type, set_icon_svg_uri) VALUES(:set_code, :set_name, :scryfall_search_url, :release_date, :set_type, :set_icon_svg_uri)');

foreach ($sets->data as &$set) {
    try
    {
        $stmt->bindValue(':set_code', $set->code);
        $stmt->bindValue(':set_name',$set->name);
        $stmt->bindValue(':scryfall_search_url',str_replace("unique=prints","unique=cards",$set->search_uri));
        $stmt->bindValue(':release_date',$set->released_at);
        $stmt->bindValue(':set_type',$set->set_type);
        $stmt->bindValue(':set_icon_svg_uri',$set->icon_svg_uri);
        $stmt->execute();
    }
    catch (PDOException $e)
    {
        // Skip for now...
    }
}

http_response_code(200);

?>