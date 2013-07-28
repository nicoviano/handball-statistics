<?php
function db_connect($dbtype, $dbhost, $dbname, $dbuser, $dbpassword) {
	$dsn = $dbtype.":dbname=".$dbname.";host=".$dbhost.";charset=utf8";
	try {
		$db = new PDO($dsn, $dbuser, $dbpassword);
	} catch (PDOException $e) {
		$db = null;
	}
	return $db;
}

function db_set_charset($db, $charset) {
	$sql = "SET CHARACTER SET ".$charset;
	$db->exec($sql);
}

function db_query($db, $sql) {
	return $db->query($sql);
}

function db_get_rows($result) {
	return $result->rowCount();
}

function db_users_get_id($db, $user) {
	return -1; // stub
}

?>
