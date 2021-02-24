<?php
    require '../../db/pdo.php';

    $post = file_get_contents('php://input');
	$post = json_decode($post);

    $username = $post->username;
    $display_name = $post->display_name;
    $pw = $post->password;

    $hash = password_hash($pw, PASSWORD_DEFAULT);

	$sql = 'INSERT INTO accounts (account_name, display_name, account_passwd) VALUES (:username, :display_name, :passwd)';
    $values = [':username' => $username, ':display_name' => $display_name, ':passwd' => $hash];

    try
    {
        $res = $pdo->prepare($query);
        $res->execute($values);
    }
    catch (PDOException $e)
    {
        echo 'Query error.';
        die();
    }
?>