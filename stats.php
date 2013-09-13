<?php
/*
 * Input variables:
 *  - action: requested action, mandatory
 *    - match
 *
 * Returned values:
 *  - XML formated data
 *    <?xml version="1.0" encoding="utf-8"?>
 *    <stats>
 *     <stat>
 *      <id.team>Team ID</id.team>
 *      <name.team>Team Name</name.team>
 *      <season.pos.2012.2013>Season Position 2012/2013</season.pos.2012.2013>
 *     </stat>
 *    </stats>
 *
 *  - match: getreturn device data
 *    * Mandatory parameters: id_team_home, id_team_away. 
 *    * returns
 *      - ErrorCode: [0,2,3,11,20]
 *      - ReturnedData: 
 *      	<email>ADDRESS</email>
 *      	<verified>BOOLEAN</verified>
 *
 */

$DEBUG = false;

extract($_GET, EXTR_PREFIX_ALL, "var");
extract($_POST, EXTR_PREFIX_ALL, "var");

// Connect to the database
require_once("db_handler.php");
require_once("db_credentials_handball.php");
$dbh = db_connect(DB_TYPE, DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD);
if ($dbh == NULL) die("No se pudo conectar con la base de datos");
db_set_charset($dbh, "utf8");

require_once("common.php");
require_once("db_betscience.php");
require_once("crypt.php");

$data = array();
$data['stats'] = array();

switch ($var_action) {
	case "teams":
		$stats_team_home = db_betscience_stats_handball_total($dbh, $var_id_team_home);
		$stats_team_away = db_betscience_stats_handball_total($dbh, $var_id_team_away);
		$data['stats'][0] = $stats_team_home;
		$data['stats'][1] = $stats_team_away;
		break;
	default:
		// 'action' parameter is not set and is mandatory
		$data['stats'][] = 1;
		break;
}

/* Write response */

if (!$DEBUG) {
	require_once("xml.php");
	//ob_clean();
	xml_set_content_type();
	$xml = xml_encode($data);
	echo $xml;
} else {
	echo "<br>";
	var_dump($data);
	//require_once("xml.php");
	$xml = xml_encode($data);
	//var_dump($xml);

	//$doc = new DOMDocument('1.0', 'utf-8');
	//$doc->formatOutput = true;
	//
	//$root = $doc->createElement('book');
	//$root = $doc->appendChild($root);
	//
	//$title = $doc->createElement('title');
	//$title = $root->appendChild($title);
	//
	//$text = $doc->createTextNode('This is the title');
	//$text = $title->appendChild($text);

	//echo $doc->saveXML();
}

?>
