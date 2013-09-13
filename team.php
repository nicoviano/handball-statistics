<?php
/*
 * Input variables:
 *  - action: requested action, mandatory
 *    - list
 *
 * Returned values:
 *  - XML formated data
 *    <?xml version="1.0" encoding="utf-8"?>
 *    <teams>
 *     <team>
 *      <id.team>Team ID</id.team>
 *      <name.team>Team Name</name.team>
 *      <season.pos.2012.2013>Season Position 2012/2013</season.pos.2012.2013>
 *     </team>
 *     <team>
 *      ...
 *     </team>
 *     ...
 *    </teams>
 *
 *  - get: return device data
 *    * Mandatory parameters: address, email. 
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
$data['teams'] = array();

switch ($var_action) {
	case "list":
		$teams = db_betscience_teams_list($dbh, 0, 2012);
		$data['teams'] = $teams;
		break;
	default:
		// 'action' parameter is not set and is mandatory
		$data['teams'][] = 1;
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
	require_once("xml.php");
	$xml = xml_encode($data);
	var_dump($data);
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
