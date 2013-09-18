<?php
/*
 * Input variables:
 *  - action: requested action, mandatory
 *    - register
 *    - login
 *  - usr: username.
 *  - pwd: password.
 *
 * Returned values:
 *  - XML formated data
 *    <?xml version="1.0" encoding="utf-8"?>
 *    <betscience>
 *     <error>ErrorCode</error>
 *     <data>ReturnedData</data>
 *    </betscience>
 *
 *    ErrorCode:
 *     0: success
 *     1: invalid action
 *     2: invalid or missing user parameter
 *     3: invalid or missing password parameter
 *     4: invalid or missing parameter
 *     10: user already registered
 *     11: user not registered
 *     12: Bad password
 *     20: DB error
 *
 * Available actions:
 *  - register: register user
 *    * Mandatory parameters: address, email. 
 *    * Additional parameters: brand, manufacturer, device, model, product, country_code, language_code.
 *    * If device (address) already registered with email, additional data is updated and e-mail sent again.
 *    * returns
 *      - ErrorCode: [0,2,3,20]
 *  - validate: validate device data
 *    * Mandatory parameters: address, email, token. 
 *    * returns
 *      - ErrorCode: [0,2,3,4,11,12,20,31] 
 *  - delete: delete device data
 *    * Mandatory parameters: address, email, token. 
 *    * returns:
 *      - ErrorCode: [0,2,3,4,11,20,31] 
 *  - get: return device data
 *    * Mandatory parameters: address, email. 
 *    * returns
 *      - ErrorCode: [0,2,3,11,20]
 *      - ReturnedData: 
 *      	<email>ADDRESS</email>
 *      	<verified>BOOLEAN</verified>
 *  - check: checks if device is Blusens Product
 *    * must provide brand, manufacturer, device, model, and product parameters
 *    * returns
 *    	- ErrorCode: [0, 11, 30]
 *
 */
$DEBUG = true;

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
$error = 0;
$data = array();

switch ($var_action) {
	case "register":
		if (($error = validate_user($var_usr)) != 0) break;
		if (($error = validate_password($var_pwd)) != 0) break;
		if (!isset($var_pwd_new)) {
			// a new user which wants to register
			if (db_betscience_users_get_id($dbh, $var_usr) >= 0) {
				// this user already exists in the table
				$error = 10;
			} else if (db_betscience_users_new($dbh, $var_usr, crypt_password($var_pwd)) == false) {
				// error inserting the user in the table
				$error = 20;
			} else if (db_betscience_users_get_id($dbh, $var_usr) < 0) {
				// the user is not found in the table
				$error = 20;
			}
		} else {
			// an existing user which wants to change the password
			if (!common_is_password_format($var_pwd_new)) {
				// 'pwd_new' needs password format
				$error = 4;
			} else if (($error = check_password($dbh, $var_usr, $var_pwd)) == 0) {
				$pdo_s = db_betscience_users_get($dbh, $var_usr);
				$data_user = $pdo_s->fetchAll();
				$id = $data_user[0]["id"];
				if (db_betscience_users_update_pwd($dbh, $id, crypt_password($var_pwd_new)) == false) {
					$error = 20;
				}
			}
		}
		break;
	case "login":
		if (($error = validate_user($var_usr)) != 0) break;
		if (($error = validate_password($var_pwd)) != 0) break;
		$error = check_password($dbh, $var_usr, $var_pwd);
		break;
	default:
		// 'action' parameter is not set and is mandatory
		$error = 1;
		break;
}

function validate_user($usr) {
	$ret = 0;
	if (!isset($usr) || !common_is_email_format($usr)) $ret = 2;
	return $ret;
}

function validate_password($pwd) {
	$ret = 0;
	if (!isset($pwd) || !common_is_password_format($pwd)) $ret = 3;
	return $ret;
}

function check_password($dbh, $usr, $pwd) {
	$ret = 0;
	if ((($pdo_s = db_betscience_users_get($dbh, $usr)) == false) || (db_get_rows($pdo_s) != 1)) {
		// the user is not registered
		$ret = 11;
	} else {
		$data_user = $pdo_s->fetchAll();
		$hash = $data_user[0]["pwd"];
		// Hashing the password with its hash as the salt returns the same hash
		if (crypt($pwd, $hash) != $hash) {
			$ret = 12;
		}
	}
	return $ret;
}


/* Write response */
require_once("xml.php");
if (!$DEBUG) {
	xml_set_content_type();
	xml_write_header();
	xml_write_tag_start("betscience");
	xml_write_tag_value("error", $error);
	xml_write_tag_end("betscience");
} else {
	echo "error ".$error."<br>";
	var_dump($data);
}

?>
