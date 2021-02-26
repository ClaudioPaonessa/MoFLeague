<?php

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: /auth/login.php");
    exit;
}

// Include DB config file
require '../../db/pdo.php';

$magic_sets_arr = array();
$magic_sets_arr["records"] = array();

$query = 'SELECT * FROM magic_sets';

$query = 'SELECT s.set_code, s.set_name, s.release_date, s.set_type, s.set_icon_svg_uri, COUNT(c.card_id) AS cards_in_db
FROM magic_sets AS s
LEFT JOIN magic_cards AS c ON s.set_id = c.magic_set_id
GROUP BY s.set_id';

try
{
    $res = $pdo->prepare($query);
    $res->execute();
}
catch (PDOException $e)
{
    echo 'Query error.';
    die();
}

//$res->fetch(PDO::FETCH_ASSOC);
//echo $res[0]["set_code"];

while ($row = $res->fetch(PDO::FETCH_ASSOC)){
    // extract row
    // this will make $row['name'] to
    // just $name only
    extract($row);

    $set_item=array(
        "set_code" => $set_code,
        "set_name" => $set_name,
        "cards_in_db" => $cards_in_db,
        "release_date" => $release_date,
        "set_type" => $set_type,
        "set_icon_svg_uri" => $set_icon_svg_uri
    );

    array_push($magic_sets_arr["records"], $set_item);
}

// set response code - 200 OK
http_response_code(200);
//header('Access-Control-Allow-Origin: *');

// show products data in json format
echo json_encode($magic_sets_arr);

?>