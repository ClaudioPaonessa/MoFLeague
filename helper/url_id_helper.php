<?php

function get_id() {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = explode( '/', $uri );

    if (!isset($uri[4])) {
        header("HTTP/1.1 404 Not Found");
        exit();
    }

    return (int) $uri[4];
}

function get_id2() {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = explode( '/', $uri );

    if (!isset($uri[5])) {
        header("HTTP/1.1 404 Not Found");
        exit();
    }

    return (int) $uri[5];
}

?>