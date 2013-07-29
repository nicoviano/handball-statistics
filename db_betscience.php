<?php
require_once("db_handler.php");

function db_betscience_users_get_id($db, $usr) {
	$id = -1;
	$sql = "SELECT * FROM `users` WHERE `usr`='$usr' LIMIT 1";
	$data = db_query($db, $sql);
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
