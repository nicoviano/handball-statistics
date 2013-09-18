<?php
require_once("db_handler.php");

function db_betscience_users_get($dbh, $usr) {
	$sql = "SELECT * FROM `users` WHERE `usr`='$usr' LIMIT 1";
	$pdo_s = db_query($dbh, $sql);
	return $pdo_s;
}

function db_betscience_users_update_pwd($dbh, $id, $pwd) {
	$sql = "UPDATE `users` SET pwd='$pwd' WHERE id='$id' LIMIT 1";
	return db_query($dbh, $sql);
}

function db_betscience_users_get_id($dbh, $usr) {
	$id = -1;
	$data = db_betscience_users_get($dbh, $usr);
	if (db_get_rows($data) == 1) {
		foreach ($data as $row){
			$id = $row["id"];
			break;
		}
	}
	return $id;
}

function db_betscience_users_new($dbh, $usr, $pwd) {
	$sql = "INSERT INTO `users` (`id`, `usr`, `pwd`) VALUES ('0', '$usr', '$pwd');";
	return db_query($dbh, $sql);
}

function db_betscience_teams_list($dbh, $id_league, $season) {
	$sql = "SELECT * FROM `teams` WHERE `id.league`='$id_league'";
	if (isset($season)) {
		$season_next = $season + 1;
		$season_label = "season.pos.{$season}.{$season_next}";
		$sql = $sql." AND `$season_label`!='NULL'";
	}
	$sth = $dbh->prepare($sql);
	$sth->execute();
	$data = $sth->fetchAll(PDO::FETCH_ASSOC);
	return $data;
}

function db_betscience_stats_handball_total_pos($dbh, $id_team, $fields, $pos) {
	$sql = "SELECT * FROM `stats_handball` WHERE `id.team.$pos`='$id_team'";
	$sth = $dbh->prepare($sql);
	$sth->execute();

	$rows = $sth->rowCount();
	if ($rows == NULL || $rows <= 0) return null;

	$data = array();
	foreach ($fields as $i => $field) {
		$data[$field] = (float)0;
	}
	$total = 0;
	while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
		$allzero = true;
		foreach ($fields as $i => $field) {
			$allzero = ($allzero and (floatval($row["$field.$pos"]) == 0));
		}
		if ($allzero) continue;
		$total++;
		foreach ($fields as $i => $field) {
			//var_dump($row[$field.".$pos"]);
			$data[$field] += floatval($row["$field.$pos"]);
		}
	}
	foreach ($fields as $i => $field) {
		$data[$field] /= $total;
	}
	$sth = null;
	return $data;
}

function stats_mean($home, $away) {
	$data = array();
	foreach ($home as $label => $value) {
		$data[$label] = ($home[$label] + $away[$label]) / 2;
	}
	return $data;
}

function db_betscience_stats_handball_total($dbh, $id_team) {
	$fields = array("shots.total.accuracy", "turnovers.total", "steals.total", "goalk.total.accuracy", "shots.blocked.total");
	$stats_home = db_betscience_stats_handball_total_pos($dbh, $id_team, $fields, "home");
	$stats_away = db_betscience_stats_handball_total_pos($dbh, $id_team, $fields, "away");
	$stats = stats_mean($stats_home, $stats_away);
	$stats["id.team"] = is_string($id_team) ? intval($id_team) : $id_team;
	return $stats;
}

function db_betscience_stats_handball_get($dbh, $id_league, $id_season, $round_league, $id_team_home, $id_team_away) {
	$id = -1;
	$sql = "SELECT `id.match` FROM `stats_handball` WHERE `id.league`='$id_league' AND `id.season`='$id_season' AND `round.league`='$round_league' AND `id.team.home`='$id_team_home' AND `id.team.away`='$id_team_away' LIMIT 1";
	$sth = $dbh->prepare($sql);
	$sth->execute();
	if ($sth->rowCount() == 1) {
		$row = $sth->fetch(PDO::FETCH_ASSOC);
		$id = $row['id.match'];
	}
	return $id;
}

function db_betscience_stats_handball_new($dbh, $id_league, $id_season, $year_season, $round_league, $id_team_home, $id_team_away) {
	$sql = "INSERT INTO `stats_handball` (`id.match`, `id.league`, `id.season`, `year.season`, `round.league`, `time.date`, `id.team.home`, `id.team.away`) VALUES ('0', '$id_league', '$id_season', '$year_season', '$round_league', NULL, '$id_team_home', '$id_team_away')";
	return db_query($dbh, $sql);
}

function db_betscience_stats_handball_update($dbh, $id, $label, $value) {
	$sql = "UPDATE `stats_handball` SET `$label`='$value' WHERE `id.match`='$id' LIMIT 1";
	return db_query($dbh, $sql);
}

function db_betscience_stats_handball_update_array($dbh, $id, $array) {
	if (is_array($array) && (count($array) > 0)) {
		$first = true;
		$sql = "UPDATE `stats_handball` SET";
		foreach($array as $key => $value) {
			if (!$first) $sql .= ",";
			if ($first) $first = false;
			if (strlen($value) > 0) {
				$sql .= " `$key`='$value'";
			} else {
				$sql .= " `$key`=NULL";
			}
		}
		$sql .= " WHERE `id.match`='$id' LIMIT 1";
		//var_dump($sql);
		//echo "<br>";
		$ret = db_query($dbh, $sql);

	}
	return $ret;
}

?>
