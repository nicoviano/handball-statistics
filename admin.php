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
	echo "	<td class='row2'>" . $row['id.team'] . "</td> \n";
	echo "	<td class='row2'>" . str_to_html($row['name.team']) . "</td> \n";
	echo "	<td class='row2'>" . $row['date.foundation'] . "</td> \n";
	echo "	<td class='row2'>" . $row['stadium.capacity'] . "</td> \n";
	echo "	<td class='row2'>" . $row['season.pos.2012.2013'] . "</td> \n";
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
	echo "</tr> \n";
}

function include_matches_row($row) {
	echo "<tr> \n";
	echo "	<td class='row2'>" . $row['id.match'] . "</td> \n";
	echo "	<td class='row2'>" . $row['round.league'] . "</td> \n";
	echo "	<td class='row2'>" . $row['id.team.home'] . "</td> \n";
	echo "	<td class='row2'>" . $row['id.team.away'] . "</td> \n";
	echo "	<td class='row2'>" . str_to_html($row['nombre.equipo.local']) . "</td> \n";
	echo "	<td class='row2'>" . str_to_html($row['nombre.equipo.visitante']) . "</td> \n";
	echo "	<td class='row2'>" . $row['score.home.final']." - ".$row['score.away.final'] . "</td> \n";
//	insert_edit_product($row);
//	insert_delete_product($row['id']);
	echo "</tr> \n"; 
}

function include_matches_footer() {
	include_table_footer();
}

function get_teams_data($dbh) {
	require_once("db_betscience.php");
	return db_betscience_teams_list($dbh, 0);
}

function check_match_new_form($round_league, $time_date, $id_team_home, $id_team_away) {
	$errors = array();
	if (!isset($round_league) || !strlen($round_league)) $errors[] = common_get_html("El campo 'Jornada' es obligatorio");
	if (strlen($round_league) && !ctype_digit($round_league)) $errors[] = common_get_html("El campo 'Jornada' tiene que ser un número");
	if (strlen($time_date)) {
		// add seconds
		if (!common_is_time_date_format($time_date)) $errors[] = common_get_html("El campo 'Fecha' no tiene el formato adecuado");
	}
	if (!isset($id_team_home) || !strlen($id_team_home) || !isset($id_team_away) || !strlen($id_team_away)) 
		$errors[] = common_get_html("Es obligatorio seleccionar los equipos");
	if (strlen($id_team_home) && strlen($id_team_away)) {
		if ($id_team_home == $id_team_away) {
			$errors[] = common_get_html("El equipo local y el equipo visitante tienen que ser distintos");
		}
	}
	return $errors;
}

function check_match_new_form_stats($stats) {
	$errors = array();
	if (is_array($stats)) {
		foreach($stats as $key => $value) {
			if (strlen($value) && !ctype_digit($value)) $errors[] = $key;
		}
	}
	return $errors;
}

function insert_match_new($dbh, $round_league, $time_date, $id_team_home, $id_team_away, $stats) {
	require_once("db_betscience.php");
	$errors = check_match_new_form($round_league, $time_date, $id_team_home, $id_team_away);
	$errors2 = check_match_new_form_stats($stats);
	$errors = array_merge($errors, $errors2);
	if (!count($errors)) {
		$id_league = 0; $id_season = 1; $year_season = 2013;
		$ret = db_betscience_stats_handball_new($dbh, $id_league, $id_season, $year_season, $round_league, $id_team_home, $id_team_away);
		if (!$ret) $errors[] = common_get_html("Error añadiendo el partido a la base de datos");
		$id = db_betscience_stats_handball_get($dbh, $id_league, $id_season, $round_league, $id_team_home, $id_team_away);
		if ($id >= 0) {
			if (strlen($time_date) > 0) {
				$time_date .= ":00"; // add seconds
				$ret = db_betscience_stats_handball_update($dbh, $id, "time.date", $time_date);
				if ($ret == false) $errors[] = common_get_html("Error guardando la fecha en la base de datos");
			}
			if (isset($stats) && is_array($stats) && (count($stats)>0)) {
				$ret = db_betscience_stats_handball_update_array($dbh, $id, $stats);
				if ($ret == false) $errors[] = common_get_html("Error guardando las estadísticas en la base de datos");
			}
		}
	}
	return $errors;
}

function showMessage($text) {
	echo "<table class='dev-table'> \n";
	echo "	<tr> \n";
	echo "	  <td class='row1' style='text-align: center;'>".$text."</td> \n";
	echo "	</tr> \n";
	echo "</table> \n";
}

function showResult($ok, $text) {
	$class = "row_red";
	if ($ok) $class = "row_green";
	echo "<table class='dev-table'> \n";
	echo "	<tr><td class='$class'>".$text."</td></tr>\n";
	echo "</table> \n";
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

require_once("common.php");

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
			$sql = "SELECT * FROM `$var_view`";
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
		case "matches_new":
			$vars = array("score.half", "score.end", "6m.goals", "6m.shots", "7m.goals", "7m.shots", "9m.goals", "9m.shots", "fastbreaks.goals", "fastbreaks.shots");
			$stats = array();
			foreach ($vars as $var) {
				$value = str_replace(".", "_", $var);
				$stats[$var.".home"] = ${"var_match_new_".$value."_home"};
				$stats[$var.".away"] = ${"var_match_new_".$value."_away"};
			}

			$show_form = true;
			if (isset($var_match_new_submit)) {
				$errors = insert_match_new($db, $var_match_new_round_league, 
					$var_match_new_time_date, $var_match_new_team_home, 
					$var_match_new_team_away, $stats);
				if (!count($errors)) {
					$ok_str = common_get_html("El partido ha sido añadido correctamente");
					showResult(true, $ok_str);
					showMessage("<a href='?view=matches_new'>Volver</a>");
					$show_form = false;
				} else {
					showResult(false, common_get_html("Error añadiendo el partido"));
					foreach ($errors as $error) {
						showResult(false, $error);
					}
				}
				//$id = isset($var_products_add_id) ? $var_products_add_id : "none";
				//if (addProduct($var_products_add_id, $var_products_add_type, $var_products_add_name, $var_products_add_batch_number, $var_products_add_batch_key,
				//$var_products_add_units, $var_products_add_brand, $var_products_add_manufacturer,
				//$var_products_add_device, $var_products_add_model, $var_products_add_product,
				//$var_products_add_mac_from, $var_products_add_mac_to)) {
				//	$ok_str = common_get_html("El producto ha sido añadido correctamente");
				//	if (isset($var_products_add_id)) $ok_str = common_get_html("El producto ha sido actualizado correctamente.");
				//	showResult(true, $ok_str);
				//	showMessage("<a href='?view=matches_new'>Volver</a>");
				//	$show_form = false;
				//}
			}

			if ($show_form) {
				require_once("form.php");
				//if ($var_products_add_type == 'DONGLE') $type_dongle = 'selected';
				//else $type_tablet = 'selected';
				$title_str = common_get_html("Añadir un partido nuevo");
				$button_str = common_get_html("Añadir partido sin estadísticas");
				$button_stats_str = common_get_html("Añadir partido con estadísticas");
				//if (isset($var_products_add_id)) {
				//	$title_str = common_get_html("Modificar el producto con ID '$var_products_add_id':");
				//	$button_str = common_get_html("Actualizar producto");
				//	$input_id = "<input type='hidden' name='products_add_id' value='$var_products_add_id'>";
				//}
				echo "<form method='post' action='?".$_SERVER['QUERY_STRING']."' class='form-horizontal'> \n";
				echo "<fieldset> \n";
				echo "<legend>$title_str</legend>";
				//echo $input_id;
				echo form_1label_1input_text("Jornada", "match_new_round_league", $var_match_new_round_league);
				echo form_1label_1input_text("Fecha", "match_new_time_date", $var_match_new_time_date, form_help("(YYYY/MM/DD HH:MM)"));
				$teams = get_teams_data($db);
				$selector_home = form_selector($teams, "match_new_team_home", $var_match_new_team_home);
				$selector_away = form_selector($teams, "match_new_team_away", $var_match_new_team_away);
				echo form_row(form_label("Partido"), $selector_home.$selector_away);
				echo "<div class='form-actions'> \n";
				echo "<button type='submit' class='btn btn-primary' name='match_new_submit' value='$button_str'>$button_str</button> \n";
				echo "</div> \n";
				echo form_1label_2input_text("Marcador parcial", "match_new_score_half_home", $var_match_new_score_half_home, "match_new_score_half_away", $var_match_new_score_half_away);
				echo form_1label_2input_text("Marcador final", "match_new_score_end_home", $var_match_new_score_end_home, "match_new_score_end_away", $var_match_new_score_end_away);
				echo form_1label_2input_text("Lanzamientos 6m", "match_new_6m_shots_home", $var_match_new_6m_shots_home, "match_new_6m_shots_away", $var_match_new_6m_shots_away);
				echo form_1label_2input_text("Goles 6m", "match_new_6m_goals_home", $var_match_new_6m_goals_home, "match_new_6m_goals_home", $var_match_new_6m_goals_home);
				echo form_1label_2input_text("Lanzamientos 7m", "match_new_7m_shots_home", $var_match_new_7m_shots_home, "match_new_7m_shots_away", $var_match_new_7m_shots_away);
				echo form_1label_2input_text("Goles 7m", "match_new_7m_goals_home", $var_match_new_7m_goals_home, "match_new_7m_goals_home", $var_match_new_7m_goals_home);
				echo form_1label_2input_text("Lanzamientos 9m", "match_new_9m_shots_home", $var_match_new_9m_shots_home, "match_new_9m_shots_away", $var_match_new_9m_shots_away);
				echo form_1label_2input_text("Goles 9m", "match_new_9m_goals_home", $var_match_new_9m_goals_home, "match_new_9m_goals_home", $var_match_new_9m_goals_home);
				echo form_1label_2input_text("Contraataques totales", "match_new_fastbreaks_total_home", $var_match_new_fastbreaks_total_home, "match_new_fastbreaks_total_away", $var_match_new_fastbreaks_total_away);
				echo form_1label_2input_text("Contraataques goles", "match_new_fastbreaks_goals_home", $var_match_new_fastbreaks_goals_home, "match_new_fastbreaks_goals_away", $var_match_new_fastbreaks_goals_away);
				echo form_1label_2input_text("Sanciones T. amarillas", "match_new_yellow_card_home", $var_match_new_yellow_card_home, "match_new_yellow_card_away", $var_match_new_yellow_card_away);
				echo form_1label_2input_text("Sanciones exclusiones 2m", "match_new_exclusion_home", $var_match_new_exclusion_home, "match_new_exclusion_away", $var_match_new_exclusion_away);
				echo form_1label_2input_text("Sanciones descalificantes", "match_new_disq_home", $var_match_new_disq_home, "match_new_disq_away", $var_match_new_disq_away);
				echo form_1label_2input_text("Asistencias", "match_new_assists_home", $var_match_new_assists_home, "match_new_assists_away", $var_match_new_assists_away);
				echo form_1label_2input_text("Penaltis a favor", "match_new_penalties_favour_home", $var_match_new_penalties_favour_home, "match_new_penalties_favour_away", $var_match_new_penalties_favour_away);
				echo form_1label_2input_text("Blocajes", "match_new_blocks_home", $var_match_new_blocks_home, "match_new_blocks_away", $var_match_new_blocks_away);
				echo form_1label_2input_text("Penaltis en contra", "match_new_penalties_against_home", $var_match_new_penalties_against_home, "match_new_penalties_against_away", $var_match_new_penalties_against_away);
				echo form_1label_2input_text("Robos", "match_new_steals_home", $var_match_new_steals_home, "match_new_steals_away", $var_match_new_steals_away);
				echo form_1label_2input_text("Pérdidas", "match_new_turnovers_home", $var_match_new_turnovers_home, "match_new_turnovers_home", $var_match_new_turnovers_home);
				echo form_1label_2input_text("Faltas técnicas", "match_new_technic_faults_home", $var_match_new_technic_faults_home, "match_new_technic_faults_away", $var_match_new_technic_faults_away);
				echo form_1label_2input_text("Faltas reglamentarias", "match_new_rules_faults_home", $var_match_new_rules_fault_home, "match_new_rules_fault_away", $var_match_new_rules_fault_away);
				echo form_row(form_label("<b>Portero</b>"));
				echo "<hr>";
				echo form_1label_2input_text("Paradas 6m", "match_new_goalk_6m_saves_home", $var_match_new_goalk_6m_saves_home, "match_new_goalk_6m_saves_away", $var_match_new_goalk_6m_saves_away);
				echo form_1label_2input_text("Tiros 6m", "match_new_goalk_6m_shots_home", $var_match_new_goalk_6m_shots_home, "match_new_goalk_6m_shots_away", $var_match_new_goalk_6m_shots_away);
				echo form_1label_2input_text("Paradas 7m", "match_new_goalk_7m_saves_home", $var_match_new_goalk_7m_saves_home, "match_new_goalk_7m_saves_away", $var_match_new_goalk_7m_saves_away);
				echo form_1label_2input_text("Tiros 7m", "match_new_goalk_7m_shots_home", $var_match_new_goalk_7m_shots_home, "match_new_goalk_7m_shots_away", $var_match_new_goalk_7m_shots_away);
				echo form_1label_2input_text("Paradas 9m", "match_new_goalk_9m_saves_home", $var_match_new_goalk_9m_saves_home, "match_new_goalk_9m_saves_away", $var_match_new_goalk_9m_saves_away);
				echo form_1label_2input_text("Tiros 9m", "match_new_goalk_9m_shots_home", $var_match_new_goalk_9m_shots_home, "match_new_goalk_9m_shots_away", $var_match_new_goalk_9m_shots_away);
				echo form_1label_2input_text("Paradas contraataque", "match_new_goalk_fastbreaks_saves_home", $var_match_new_goalk_fastbreaks_saves_home, "match_new_goalk_fastbreaks_saves_away", $var_match_new_goalk_fastbreaks_saves_away);
				echo form_1label_2input_text("Tiros contraataque", "match_new_goalk_fastbreaks_shots_home", $var_match_new_goalk_fastbreaks_shots_home, "match_new_goalk_fastbreaks_shots_away", $var_match_new_goalk_fastbreaks_shots_away);

				echo "<div class='form-actions'> \n";
				echo "<button type='submit' class='btn btn-primary' name='match_new_submit' value='$button_stats_str'>$button_stats_str</button> \n";
				echo "</div> \n";
				echo "</fieldset> \n";
				echo "</form> \n";

				//echo "<tr> \n";
				//echo "	<td class='row1'>Referencia: </td> \n";
				//echo "	<td class='row2'><input type='text' name='products_add_name' value='$var_products_add_name'></td> \n";
				//echo "</tr> \n"; 
				//echo "<tr> \n";
				//echo "	<td class='row1'>Lote (N&uacute;mero): </td> \n";
				//echo "	<td class='row2'><input type='text' name='products_add_batch_number' value='$var_products_add_batch_number'></td> \n";
				//echo "</tr> \n"; 
				//echo "<tr> \n";
				//echo "	<td class='row1'>Lote (Clave): </td> \n";
				//echo "	<td class='row2'><input type='text' name='products_add_batch_key' value='$var_products_add_batch_key'></td> \n";
				//echo "</tr> \n"; 
				//echo "<tr> \n";
				//echo "	<td class='row1'>Unidades: </td> \n";
				//echo "	<td class='row2'><input type='text' name='products_add_units' value='$var_products_add_units'></td> \n";
				//echo "</tr> \n"; 
				//echo "<tr> \n";
				//echo "	<td class='row1'>Brand: </td> \n";
				//echo "	<td class='row2'><input type='text' name='products_add_brand' value='$var_products_add_brand'></td> \n";
				//echo "</tr> \n"; 
				//echo "<tr> \n";
				//echo "	<td class='row1'>Manufacturer: </td> \n";
				//echo "	<td class='row2'><input type='text' name='products_add_manufacturer' value='$var_products_add_manufacturer'></td> \n";
				//echo "</tr> \n"; 
				//echo "<tr> \n";
				//echo "	<td class='row1'>Device: </td> \n";
				//echo "	<td class='row2'><input type='text' name='products_add_device' value='$var_products_add_device'></td> \n";
				//echo "</tr> \n"; 
				//echo "<tr> \n";
				//echo "	<td class='row1'>Model: </td> \n";
				//echo "	<td class='row2'><input type='text' name='products_add_model' value='$var_products_add_model'></td> \n";
				//echo "</tr> \n"; 
				//echo "<tr> \n";
				//echo "	<td class='row1'>Product: </td> \n";
				//echo "	<td class='row2'><input type='text' name='products_add_product' value='$var_products_add_product'></td> \n";
				//echo "</tr> \n"; 
				//echo "<tr> \n";
				//echo "	<td class='row1'>MAC Inicio: </td> \n";
				//echo "	<td class='row2'><input type='text' name='products_add_mac_from' value='$var_products_add_mac_from'></td> \n";
				//echo "</tr> \n"; 
				//echo "<tr> \n";
				//echo "	<td class='row1'>MAC Final: </td> \n";
				//echo "	<td class='row2'><input type='text' name='products_add_mac_to' value='$var_products_add_mac_to'></td> \n";
				//echo "</tr> \n"; 
				//echo "<tr> \n";
				//echo "	<td class='row2'><input type='submit' name='match_add_submit' value='$button_str'></td> \n";
				//echo "</tr> \n"; 

			}
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
