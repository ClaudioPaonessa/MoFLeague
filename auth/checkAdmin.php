<?php

if (!isset($_SESSION["admin"]) || boolval($_SESSION["admin"]) !== true) {
    http_response_code(401);
    
    $errorData = array();
    $errorData["error"] = "You are not authorized to execute this action.";

    echo json_encode($errorData);
    die();
}

?>