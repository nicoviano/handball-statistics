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
 *     2: invalid user
 *     3: invalid password
 *     10: already registered
 *     11: login failed. User not registered
 *     12: login failed. Bad password
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
$db = db_connect(DB_TYPE, DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD);
if ($db == NULL) die("No se pudo conectar con la base de datos");
db_set_charset($db, "utf8");

require_once("db_betscience.php");
require_once("crypt.php");
$error = 0;
$data = array();

switch ($var_action) {
	case "get":
		if (!isset($var_usr)) {
			$error = 2;
		} else if (!isset($var_pwd)) {
			$error = 3;
		} else {
			$id = devices_get_id($var_email, $var_address);
			if ($id < 0) {
				$error = 11;
			} else {
				$data= db_handler_devices_get($id);
				//$data['vendor'] = devices_get_vendor($data['mac']);
			}
		}
		break;
	case "register":
		if (!isset($var_usr)) {
			// 'usr' param is mandatory
			$error = 2;
		} else if (!isset($var_pwd)) {
			// 'pwd' param is mandatory
			$error = 3;
		} else if (db_betscience_users_get_id($db, $var_usr) >= 0) {
			// this user already exists in the table
			$error = 10;
		} else if (db_betscience_users_new($db, $var_usr, crypt_password($var_pwd)) == false) {
			// error inserting the user in the table
			$error = 20;
		} else if (db_betscience_users_get_id($db, $var_usr) < 0) {
			// the user is not found in the table
			$error = 20;
		}
				//if (devices_register($var_email, $var_address)) {
				//	if (($id = devices_get_id($var_email, $var_address)) >= 0) {
				//		if (devices_update($id, $var_email, $var_address, 
				//			$var_brand, $var_manufacturer, $var_device, $var_model, $var_product, 
				//			$var_country_code, $var_language_code)) {
				//				if (devices_set_token($id, mt_rand()) && send_mail_pear($id)) {
				//					$error = 0;
				//				} else $error = 20;
				//		} else $error = 20;
				//	} else $error = 20;
				//} else $error = 20;
			//
				//if (devices_update($id, $var_email, $var_address, 
				//	$var_brand, $var_manufacturer, $var_device, $var_model, $var_product, 
				//	$var_country_code, $var_language_code)) {
				//		if (devices_set_token($id, mt_rand()) && send_mail_pear($id)) {
				//			$error = 0;
				//		} else $error = 20;
				//} else $error = 20;
		break;
	case "login":
		if (!isset($var_usr)) {
			// 'usr' param is mandatory
			$error = 2;
		} else if (!isset($var_pwd)) {
			// 'pwd' param is mandatory
			$error = 3;
		} else if ((($pdo_s = db_betscience_users_get($db, $var_usr)) == false)||(db_get_rows($pdo_s) != 1)) {
			// the user is not registered
			$error = 11;
		} else {
			$data = $pdo_s->fetchAll();
			$hash = $data[0]["pwd"];
			// Hashing the password with its hash as the salt returns the same hash
			if (crypt($var_pwd, $hash) == $hash) {
				$error = 0;
			} else {
				$error = 12;
			}
		}
		break;
	default:
		$error = 1;
		break;
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
}

?>
