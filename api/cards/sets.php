<?php

session_start();

require_once '../../auth/checkLogin.php';
require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';

$magicSetsArr = array();
$magicSetsArr["records"] = array();

$query = 'SELECT s.set_id, s.set_code, s.set_name, s.release_date, s.set_type, s.set_icon_svg_uri, COUNT(c.card_id) AS cards_in_db
            FROM magic_sets AS s
            LEFT JOIN magic_cards AS c ON s.set_id = c.magic_set_id
            GROUP BY s.set_id';

$res = executeSQL($query);

while ($row = $res->fetch(PDO::FETCH_ASSOC)){
    // extract row
    // this will make $row['name'] to
    // just $name only
    extract($row);

    $setItem=array(
        "setId" => $set_id,
        "setCode" => $set_code,
        "setName" => $set_name,
        "cardsInDB" => $cards_in_db,
        "releaseYear" => substr($release_date, 0, 4),
        "setType" => $set_type,
        "setIconSVGUri" => $set_icon_svg_uri
    );

    array_push($magicSetsArr["records"], $setItem);
}

http_response_code(200);

echo json_encode($magicSetsArr);

?>