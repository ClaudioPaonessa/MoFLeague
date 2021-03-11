<?php

function returnError($message) {
    http_response_code(500);
    
    $errorData = array();
    $errorData["error"] = $message;

    echo json_encode($errorData);
    die();
}

?>