<?php

require_once '../../helper/errorHelper.php';
require_once '../../helper/dbHelper.php';

function getProfile($accountId) {
    $query = 'SELECT a.account_id, a.display_name, a.mtg_arena_name, a.admin_privilege
        FROM accounts AS a
        WHERE (a.account_id = :account_id)';

    $values = [':account_id' => $accountId];

    $res = executeSQL($query, $values);
    $row = $res->fetch(PDO::FETCH_ASSOC);
    
    if (is_array($row)) {
        extract($row);

        $profile=array(
            "accountId" => $account_id,
            "displayName" => $display_name,
            "mtgArenaName" => $mtg_arena_name,
            "adminPrivilege" => $admin_privilege
        );

        return $profile;
    }

    returnError("Profile not found");
}

?>