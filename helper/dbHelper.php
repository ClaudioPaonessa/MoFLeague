<?php

require_once 'errorHelper.php';
require_once '../../db/pdo.php';

function executeSQL($query, $values = null) {
    global $pdo;
    
    try
    {
        $res = $pdo->prepare($query);
        $res->execute($values);
    }
    catch (PDOException $e)
    {
        echo $e;
        returnError("Error in SQL query.");
    }

    return $res;
}

?>