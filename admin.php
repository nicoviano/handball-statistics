<?php

function is_session_authenticated() {  
	return $_SESSION["isAuthenticated"];
}

function reload() {
	echo "<script type='text/javascript'>location.reload(true);</script>";
}

function str_to_html($str) {
	return htmlentities($str, ENT_QUOTES | ENT_IGNORE, "UTF-8");
}

function include_table_header($class) {
	echo "<table class='".$class."'> \n";
}

function include_table_footer() {
	echo "</table> \n";
}

function include_teams_header() {
	include_table_header("dev-table");
	echo "<tr> \n";
	echo "	<td class='row1'>ID</td> \n";
	echo "	<td class='row1'>Nombre de Equipo</td> \n";
	echo "	<td class='row1'>Fecha de creaci&oacute;n</td> \n";
	echo "	<td class='row1'>Capacidad del Estadio</td> \n";
	echo "	<td class='row1'>Puesto 2012/2013</td> \n";
	echo "</tr> \n";
}

function include_teams_row($row) {
	echo "<tr> \n";
	echo "	<td class='row2'>" . $row['id.equipo'] . "</td> \n";
	echo "	<td class='row2'>" . str_to_html($row['nombre.equipo']) . "</td> \n";
	echo "	<td class='row2'>" . $row['fecha.creacion'] . "</td> \n";
	echo "	<td class='row2'>" . $row['capacidad.estadio'] . "</td> \n";
	echo "	<td class='row2'>" . $row['puesto.temp.12.13'] . "</td> \n";
//	insert_edit_product($row);
//	insert_delete_product($row['id']);
	echo "</tr> \n"; 
}

function include_teams_footer() {
	include_table_footer();
}

function include_matches_header() {
	include_table_header("dev-table");
	echo "<tr> \n";
	echo "	<td class='row1'>ID Partido</td> \n";
	echo "	<td class='row1'>Jornada</td> \n";
	echo "	<td class='row1'>ID Equipo Local</td> \n";
	echo "	<td class='row1'>ID Equipo Visitante</td> \n";
	echo "	<td class='row1'>Equipo Local</td> \n";
	echo "	<td class='row1'>Equipo Visitante</td> \n";
	echo "	<td class='row1'>Resultado</td> \n";
	echo "	<td class='row1'>Espectadores</td> \n";
	echo "</tr> \n";
}

function include_matches_row($row) {
	echo "<tr> \n";
	echo "	<td class='row2'>" . $row['id.partido'] . "</td> \n";
	echo "	<td class='row2'>" . $row['num.jornada'] . "</td> \n";
	echo "	<td class='row2'>" . $row['id.eq.local'] . "</td> \n";
	echo "	<td class='row2'>" . $row['id.eq.visitante'] . "</td> \n";
	echo "	<td class='row2'>" . str_to_html($row['nombre.equipo.local']) . "</td> \n";
	echo "	<td class='row2'>" . str_to_html($row['nombre.equipo.visitante']) . "</td> \n";
	echo "	<td class='row2'>" . $row['goles.marcados.local.fin.partido']." - ".$row['goles.marcados.visitante.fin.partido'] . "</td> \n";
	echo "	<td class='row2'>" . $row['num.espectadores.asistentes.partido.vieja'] . "</td> \n";
//	insert_edit_product($row);
//	insert_delete_product($row['id']);
	echo "</tr> \n"; 
}

function include_matches_footer() {
	include_table_footer();
}

session_start();

//import_request_variables("GP", "var_"); // obsolete
extract($_GET, EXTR_PREFIX_ALL, "var");
extract($_POST, EXTR_PREFIX_ALL, "var");

// Connect to the database
require_once("db_handler.php");
require_once("db_credentials_handball.php");
$db = db_connect(DB_TYPE, DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD);
if ($db == NULL) die("No se pudo conectar con la base de datos");
db_set_charset($db, "utf8");

// check credentials to access the admin web page
if (!is_session_authenticated() && $var_user != NULL && $var_password != NULL) {
	$sql = "SELECT * FROM `webusers` WHERE `user`='$var_user' AND `password`='$var_password'";
	$result = db_query($db, $sql);
	if (db_get_rows($result) == 1) {
		$_SESSION["isAuthenticated"] = true;
		$_SESSION["user"] = $var_user;
	}
//	foreach ($result as $row) {
//		var_dump($row);
	//}
}

//require_once("common.php");

include('html_header.php');

if (!is_session_authenticated()) {
	include('login.php');
} else {
	include('header.php');
	switch ($var_view) {
		default:
			$var_view = "teams";
		case "matches":
		case "teams":
			$sql = "SELECT * FROM `".$var_view."`";
			$result = db_query($db, $sql);
			$a = db_get_rows($result);
			$header_func = "include_".$var_view."_header";
			call_user_func($header_func);
			foreach ($result as $row){
				$row_func = "include_".$var_view."_row";
				call_user_func($row_func, $row);
			}
			$footer_func = "include_".$var_view."_footer";
			call_user_func($footer_func);
			break;
		//case "matches":
		//	$sql = "SELECT * FROM `partidos`";
		//	$result = db_query($db, $sql);
		//	include_matches_header();
		//	foreach ($result as $row){
		//		include_matches_row($row);
		//	}
		//	include_matches_footer();
		//	break;
		case "logout":
			$_SESSION["isAuthenticated"] = false;
			reload();
			break;
	}
}

include('html_footer.php');

?>
