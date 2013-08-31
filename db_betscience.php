<?php
require_once("db_handler.php");

function db_betscience_users_get($db, $usr) {
	$sql = "SELECT * FROM `users` WHERE `usr`='$usr' LIMIT 1";
	$pdo_s = db_query($db, $sql);
	return $pdo_s;
}

function db_betscience_users_get_id($db, $usr) {
	$id = -1;
	$data = db_betscience_users_get($db, $usr);
	if (db_get_rows($data) == 1) {
		foreach ($data as $row){
			$id = $row["id"];
			break;
		}
	}
	return $id;
}

function db_betscience_users_new($db, $usr, $pwd) {
	$sql = "INSERT INTO `users` (`id`, `usr`, `pwd`) VALUES ('0', '$usr', '$pwd');";
	return db_query($db, $sql);
}

?>
