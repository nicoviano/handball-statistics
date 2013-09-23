<?php

function is_session_authenticated() {  
	return $_SESSION["isAuthenticated"];
}

function reload() {
	echo "<script type='text/javascript'>location.reload(true);</script>";
}

function generate_table($dbh, $sql, $var_view) {
	$sth = $dbh->prepare($sql);
	$sth->execute();
	$first = true;
	while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
		if ($first) {
			$header_func = "include_".$var_view."_header";
			call_user_func($header_func, $row);
			$first = false;
		}
		$row_func = "include_".$var_view."_row";
		call_user_func($row_func, $row);
	}
	$footer_func = "include_".$var_view."_footer";
	call_user_func($footer_func);
}

function insert_button_match_info($row) {
	$title_msg = common_get_html("Ver detalles");
	$match_id = $row['id.match'];
	$action = "?view=match_info&match_id=$match_id";
	echo "<a class='btn btn-small' href='$action' title='$title_msg'><i class='icon-info-sign'></i></a>";
}

//function insert_edit_product($row) {
//	$query_msg = common_get_html("¿Estás seguro de que quieres editar el producto con ID {$row['id']}?");
//	$title_msg = common_get_html("Editar producto");
//	$action = "?view=add_product";
//	foreach ($row as $key => $value) {
//		if (($key == "mac_from" || $key == "mac_to") && ($value == null)) {
//			$value = "";
//		}
//		if (!is_numeric($key)) {
//			$row["products_add_".$key] = $value;
//		}
//		unset($row[$key]);
//	}
//	$data = json_encode($row);
//	$quotes_search = array("'", "\"");
//	$quotes_replace = array("@@apos@@", "@@quot@@");
//	$data2 = str_replace($quotes_search, $quotes_replace, $data);
//	echo "<div class='icon16 link' onclick='queryUserForActionWithData(\"$query_msg\", \"$action\", \"$data2\");' title='$title_msg'><img src='img/pencil_16.png'></div>\n";
//}

function include_table_header($class) {
	echo "<table class='".$class."'> \n";
}

function include_table_footer() {
	echo "</table> \n";
}

function include_teams_header($row) {
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
	echo "	<td class='row2'>" . common_get_html($row['name.team']) . "</td> \n";
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

function include_matches_header($row) {
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
	echo "	<td class='row2'>" . common_get_html($row['nombre.equipo.local']) . "</td> \n";
	echo "	<td class='row2'>" . common_get_html($row['nombre.equipo.visitante']) . "</td> \n";
	echo "	<td class='row2'>" . $row['score.home.final']." - ".$row['score.away.final'] . "</td> \n";
//	insert_edit_product($row);
//	insert_delete_product($row['id']);
	echo "</tr> \n"; 
}

function include_matches_footer() {
	include_table_footer();
}

function include_stats_handball_header($row) {
	include_table_header("dev-table");
	echo "<tr> \n";
	echo "	<td class='row1'>ID Partido</td> \n";
	echo "	<td class='row1'>Fecha</td> \n";
	echo "	<td class='row1'>Temporada</td> \n";
	echo "	<td class='row1'>Jornada</td> \n";
	echo "	<td class='row1'>ID</td> \n";
	echo "	<td class='row1'>Equipo Local</td> \n";
	echo "	<td class='row1'>ID</td> \n";
	echo "	<td class='row1'>Equipo Visitante</td> \n";
	echo "	<td class='row1'>Resultado</td> \n";
	echo "	<td class='row1'></td> \n";
	echo "</tr> \n";
}

function include_stats_handball_row($row) {
	$year_plus = $row['year.season']+1;
	echo "<tr> \n";
	echo "	<td class='row2'>" . $row['id.match'] . "</td> \n";
	echo "	<td class='row2'>" . $row['time.date'] . "</td> \n";
	echo "	<td class='row2'>" . $row['year.season']."/".$year_plus. "</td> \n";
	echo "	<td class='row2'>" . $row['round.league'] . "</td> \n";
	echo "	<td class='row2'>" . $row['id.team.home'] . "</td> \n";
	echo "	<td class='row2'>" . common_get_html($row['name.team.home']) . "</td> \n";
	echo "	<td class='row2'>" . $row['id.team.away'] . "</td> \n";
	echo "	<td class='row2'>" . common_get_html($row['name.team.away']) . "</td> \n";
	echo "	<td class='row2'>" . $row['score.end.home']." - ".$row['score.end.away'] . "</td> \n";
	echo "	<td class='row2'> \n";
	insert_button_match_info($row);
	echo "  </td> \n";
	echo "</tr> \n"; 
}

function include_stats_handball2_header($row) {
	include_table_header("table table-bordered table-condensed");
	echo "<tr> \n";
	foreach($row as $tag => $value) {
		echo "	<td class='row1'><b>$tag</b></td> \n";
		//echo "	<td class='row1'>$tag</td> \n";
	}
	echo "</tr> \n";
}

function include_stats_handball2_row($row) {
	echo "<tr> \n";
	foreach($row as $tag => $value) {
		echo "	<td class='row2'>".common_get_html($value)."</td> \n";
		//echo "	<td class='row2'>".common_get_html($value)."</td> \n";
	}
	echo "</tr> \n"; 
}

function include_stats_handball_footer() {
	include_table_footer();
}

function get_teams_data($dbh) {
	require_once("db_betscience.php");
	return db_betscience_teams_list($dbh, 0);
}

function match_info_table($dbh, $id) {
	$str = "";
	$str .= "<table class='table table-bordered'> \n";
	$sql = "SELECT `s`.*, `t1`.`name.team` AS `name.team.home`, `t2`.`name.team` AS `name.team.away` FROM `stats_handball` AS s LEFT JOIN `teams` as t1 ON `id.team.home`=`t1`.`id.team` LEFT JOIN `teams` as t2 ON `id.team.away`=`t2`.`id.team` WHERE `id.match`='$id' LIMIT 1";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	$row = $sth->fetch(PDO::FETCH_ASSOC);
	foreach($row as $key => $value) {
		$str .= "<tr> \n  <td class='row1'>$key</td> \n  <td class='row2'>".common_get_html($value)."</td> \n</tr> \n"; 
	}
	$sth = null;
	$str .= "</table> \n";
	return $str;
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

function match_new_input_stats2_check($str) {
	return (preg_match("/^\d+\/\d+=\d+%$/", $str) || preg_match("/^\d+\/\d+$/", $str));
}

function match_new_input_stats2_get($str) {
	if (preg_match("/^\d+\/\d+=\d+%$/", $str)) {
		preg_match("/^(\d+)\/(\d+)=(\d+)%$/", $str, $match);
	} else if (preg_match("/^\d+\/\d+$/", $str)) {
		preg_match("/^(\d+)\/(\d+)$/", $str, $match);
		if ($match[2] == 0) $match[] = 0;
		else $match[] = intval($match[1] / $match[2] * 100);
	}
	return $match;
}

function check_match_new_form_stats2($stats) {
	$errors = array();
	if (is_array($stats)) {
		foreach($stats as $key => $value) {
			foreach($value as $key2 => $value2) {
				if (strlen($value2) && !match_new_input_stats2_check($value2)) $errors[] = $key2.".".$key;
			}
		}
	}
	return $errors;
}

function transform_stats2($stats) {
	$stats_new = array();
	if (is_array($stats)) {
		foreach($stats as $key => $value) {
			foreach($value as $key2 => $value2) {
				$match = match_new_input_stats2_get($value2);
				$tag = ".goals.";
				if (strpos($key2, "goalk") !== false) $tag = ".saves.";
				$stats_new[$key2.$tag.$key] = $match[1];
				$stats_new[$key2.".shots.".$key] = $match[2];
				$stats_new[$key2.".accuracy.".$key] = $match[3];
			}
		}
	}
	return $stats_new;
}

function add_dependent_stats_tag(&$stats_new, $tag) {
	$stats_new["total.goals.$tag"] = $stats_new["6m.goals.$tag"] + $stats_new["7m.goals.$tag"] + $stats_new["9m.goals.$tag"] + $stats_new["fastbreaks.goals.$tag"];
	$stats_new["total.shots.$tag"] = $stats_new["6m.shots.$tag"] + $stats_new["7m.shots.$tag"] + $stats_new["9m.shots.$tag"] + $stats_new["fastbreaks.shots.$tag"];
	$value = 0;
	if ($stats_new["total.shots.$tag"] !== 0) $value = intval($stats_new["total.goals.$tag"] / $stats_new["total.shots.$tag"] * 100);
	$stats_new["total.accuracy.$tag"] = $value;

	$stats_new["goalk.total.saves.$tag"] = $stats_new["goalk.6m.saves.$tag"] + $stats_new["goalk.7m.saves.$tag"] + $stats_new["goalk.9m.saves.$tag"] + $stats_new["goalk.fastbreaks.saves.$tag"];
	$stats_new["goalk.total.shots.$tag"] = $stats_new["goalk.6m.shots.$tag"] + $stats_new["goalk.7m.shots.$tag"] + $stats_new["goalk.9m.shots.$tag"] + $stats_new["goalk.fastbreaks.shots.$tag"];
	$value = 0;
	if ($stats_new["goalk.total.shots.$tag"] !== 0) $value = intval($stats_new["goalk.total.saves.$tag"] / $stats_new["goalk.total.shots.$tag"] * 100);
	$stats_new["goalk.total.accuracy.$tag"] = $value;

	$stats_new["turnovers.total.$tag"] = $stats_new["turnovers.$tag"] + $stats_new["technic.faults.$tag"] + $stats_new["rules.faults.$tag"];
}

function add_dependent_stats(&$stats) {
	add_dependent_stats_tag($stats, "home");
	add_dependent_stats_tag($stats, "away");
}


function insert_match_new($dbh, $round_league, $time_date, $id_team_home, $id_team_away, $stats, $stats2) {
	require_once("db_betscience.php");

	// check params
	$errors = check_match_new_form($round_league, $time_date, $id_team_home, $id_team_away);
	$errors1 = check_match_new_form_stats($stats);
	$errors2 = check_match_new_form_stats2($stats2);
	$errors = array_merge($errors, $errors1, $errors2);
	if (count($errors)) return $errors;

	$id_league = 0; $id_season = 1; $year_season = 2013;
	// check if this match already exists
	$array = array(
		"id.league"=>$id_league, "id.season"=>$id_season, 
		"id.team.home"=>$id_team_home, "id.team.away"=>$id_team_away
	);
	$row = db_betscience_stats_handball_get_from_array($dbh, $array);
	if (is_array($row) && ($row['round.league'] != $round_league)) {
		// match found. round in the season does not match. throw error.
		$round_saved = $row['round.league'];
		$year_season_plus1 = $year_season+1;
		$errors[] = common_get_html("El partido ya existe en la jornada $round_saved de la temporada $year_season/$year_season_plus1");
		return $errors;
	} else {
		if (!$row) {
			// match not found. Add it.
			$ret = db_betscience_stats_handball_new($dbh, $id_league, $id_season, $year_season, $round_league, $id_team_home, $id_team_away);
			if (!$ret) {
				$errors[] = common_get_html("Error añadiendo el partido a la base de datos");
				return $errors;
			}

			// check that the match was added
			$array = array(
				"id.league"=>$id_league, "id.season"=>$id_season, 
				"round.league"=>$round_league, 
				"id.team.home"=>$id_team_home, "id.team.away"=>$id_team_away
			);
			$row = db_betscience_stats_handball_get_from_array($dbh, $array);
			if (!$row) {
				$errors[] = common_get_html("Error añadiendo el partido a la base de datos");
				return $errors;
			}
		}
		$id = $row['id.match'];
		if (isset($time_date) && strlen($time_date) > 0) {
			$time_date .= ":00"; // add seconds
			if (!is_array($stats)) $stats = array();
			$stats['time.date'] = $time_date;
		}
		if (isset($stats2) && is_array($stats2)) {
			$stats2_new = transform_stats2($stats2);
			$stats = array_merge($stats, $stats2_new);
			add_dependent_stats($stats);
		}
		if (isset($stats) && is_array($stats) && (count($stats)>0)) {
			$ret = db_betscience_stats_handball_update_array($dbh, $id, $stats);
			if ($ret == false) $errors[] = common_get_html("Error guardando las estadísticas en la base de datos");
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
		case "teams":
			$sql = "SELECT * FROM `$var_view`";
			generate_table($db, $sql, $var_view);
			break;
		case "stats_handball":
			$sql = "SELECT `s`.*, `t1`.`name.team` AS `name.team.home`, `t2`.`name.team` AS `name.team.away` FROM `$var_view` AS s LEFT JOIN `teams` as t1 ON `id.team.home`=`t1`.`id.team` LEFT JOIN `teams` as t2 ON `id.team.away`=`t2`.`id.team`";
			generate_table($db, $sql, $var_view);
			break;
		case "match_info":
			if (!isset($var_match_id)) showResult(false, common_get_html("¡Error! ID de Partido no especificado"));
			else echo match_info_table($db, $var_match_id);
			break;
		case "matches_new":
			$show_form = true;
			if (isset($var_match_new_submit)) {
				// stats without additional treatment
				$vars = array("score.half", "score.end", "yellow.card", "exclusion", "disq", "assists", "penalties.favour", "blocks", "penalties.against", "steals", "turnovers", "technic.faults", "rules.faults");
				$stats = array();
				foreach ($vars as $var) {
					$value = str_replace(".", "_", $var);
					$stats[$var.".home"] = ${"var_match_new_".$value."_home"};
					$stats[$var.".away"] = ${"var_match_new_".$value."_away"};
				}
				// stats with need additional treatment
				$vars2 = array("6m", "7m", "9m", "fastbreaks", "goalk.6m", "goalk.7m", "goalk.9m", "goalk.fastbreaks");
				$stats2 = array("home"=>array(), "away"=>array());
				foreach ($vars2 as $var) {
					$value = str_replace(".", "_", $var);
					$stats2["home"][$var] = ${"var_match_new_".$value."_home"};
					$stats2["away"][$var] = ${"var_match_new_".$value."_away"};
				}
				$errors = insert_match_new($db, $var_match_new_round_league, 
					$var_match_new_time_date, $var_match_new_team_home, 
					$var_match_new_team_away, $stats, $stats2);
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
			}

			if ($show_form) {
				require_once("form.php");
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
				echo form_row(form_label("<b>Goles y Lanzamientos</b>"));
				echo "<hr>";
				echo form_1label_2input_text("6m", "match_new_6m_home", $var_match_new_6m_home, "match_new_6m_away", $var_match_new_6m_away);
				//echo form_1label_2input_text("Goles 6m", "match_new_6m_goals_home", $var_match_new_6m_goals_home, "match_new_6m_goals_home", $var_match_new_6m_goals_home);
				echo form_1label_2input_text("7m", "match_new_7m_home", $var_match_new_7m_home, "match_new_7m_away", $var_match_new_7m_away);
				//echo form_1label_2input_text("Goles 7m", "match_new_7m_goals_home", $var_match_new_7m_goals_home, "match_new_7m_goals_home", $var_match_new_7m_goals_home);
				echo form_1label_2input_text("9m", "match_new_9m_home", $var_match_new_9m_home, "match_new_9m_away", $var_match_new_9m_away);
				//echo form_1label_2input_text("Goles 9m", "match_new_9m_goals_home", $var_match_new_9m_goals_home, "match_new_9m_goals_home", $var_match_new_9m_goals_home);
				echo form_1label_2input_text("Contraataque", "match_new_fastbreaks_home", $var_match_new_fastbreaks_home, "match_new_fastbreaks_away", $var_match_new_fastbreaks_away);
				//echo form_1label_2input_text("Contraataques goles", "match_new_fastbreaks_goals_home", $var_match_new_fastbreaks_goals_home, "match_new_fastbreaks_goals_away", $var_match_new_fastbreaks_goals_away);
				echo form_row(form_label("<b>Ataque</b>"));
				echo "<hr>";
				echo form_1label_2input_text("Asistencias", "match_new_assists_home", $var_match_new_assists_home, "match_new_assists_away", $var_match_new_assists_away);
				echo form_1label_2input_text("Penaltis a favor", "match_new_penalties_favour_home", $var_match_new_penalties_favour_home, "match_new_penalties_favour_away", $var_match_new_penalties_favour_away);
				echo form_1label_2input_text("Pérdidas", "match_new_turnovers_home", $var_match_new_turnovers_home, "match_new_turnovers_away", $var_match_new_turnovers_away);
				echo form_row(form_label("<b>Defensa</b>"));
				echo "<hr>";
				echo form_1label_2input_text("Blocajes", "match_new_blocks_home", $var_match_new_blocks_home, "match_new_blocks_away", $var_match_new_blocks_away);
				echo form_1label_2input_text("Penaltis en contra", "match_new_penalties_against_home", $var_match_new_penalties_against_home, "match_new_penalties_against_away", $var_match_new_penalties_against_away);
				echo form_1label_2input_text("Robos", "match_new_steals_home", $var_match_new_steals_home, "match_new_steals_away", $var_match_new_steals_away);

				echo form_1label_2input_text("Faltas técnicas", "match_new_technic_faults_home", $var_match_new_technic_faults_home, "match_new_technic_faults_away", $var_match_new_technic_faults_away);
				echo form_1label_2input_text("Faltas reglamentarias", "match_new_rules_faults_home", $var_match_new_rules_faults_home, "match_new_rules_faults_away", $var_match_new_rules_faults_away);
				echo form_row(form_label("<b>Sanciones</b>"));
				echo "<hr>";
				echo form_1label_2input_text("Tarjetas amarillas", "match_new_yellow_card_home", $var_match_new_yellow_card_home, "match_new_yellow_card_away", $var_match_new_yellow_card_away);
				echo form_1label_2input_text("Exclusiones 2m", "match_new_exclusion_home", $var_match_new_exclusion_home, "match_new_exclusion_away", $var_match_new_exclusion_away);
				echo form_1label_2input_text("Descalificantes", "match_new_disq_home", $var_match_new_disq_home, "match_new_disq_away", $var_match_new_disq_away);

				echo form_row(form_label("<b>Portero</b>"));
				echo "<hr>";
				echo form_1label_2input_text("6m", "match_new_goalk_6m_home", $var_match_new_goalk_6m_home, "match_new_goalk_6m_away", $var_match_new_goalk_6m_away);
				//echo form_1label_2input_text("Tiros 6m", "match_new_goalk_6m_shots_home", $var_match_new_goalk_6m_shots_home, "match_new_goalk_6m_shots_away", $var_match_new_goalk_6m_shots_away);
				echo form_1label_2input_text("7m", "match_new_goalk_7m_home", $var_match_new_goalk_7m_home, "match_new_goalk_7m_away", $var_match_new_goalk_7m_away);
				//echo form_1label_2input_text("Tiros 7m", "match_new_goalk_7m_shots_home", $var_match_new_goalk_7m_shots_home, "match_new_goalk_7m_shots_away", $var_match_new_goalk_7m_shots_away);
				echo form_1label_2input_text("9m", "match_new_goalk_9m_home", $var_match_new_goalk_9m_home, "match_new_goalk_9m_away", $var_match_new_goalk_9m_away);
				//echo form_1label_2input_text("Tiros 9m", "match_new_goalk_9m_shots_home", $var_match_new_goalk_9m_shots_home, "match_new_goalk_9m_shots_away", $var_match_new_goalk_9m_shots_away);
				echo form_1label_2input_text("Contraataque", "match_new_goalk_fastbreaks_home", $var_match_new_goalk_fastbreaks_home, "match_new_goalk_fastbreaks_away", $var_match_new_goalk_fastbreaks_away);
				//echo form_1label_2input_text("Tiros contraataque", "match_new_goalk_fastbreaks_shots_home", $var_match_new_goalk_fastbreaks_shots_home, "match_new_goalk_fastbreaks_shots_away", $var_match_new_goalk_fastbreaks_shots_away);

				echo "<div class='form-actions'> \n";
				echo "<button type='submit' class='btn btn-primary' name='match_new_submit' value='$button_stats_str'>$button_stats_str</button> \n";
				echo "</div> \n";
				echo "</fieldset> \n";
				echo "</form> \n";
			}
			break;
		case "logout":
			$_SESSION["isAuthenticated"] = false;
			reload();
			break;
	}
}

include('html_footer.php');

?>
