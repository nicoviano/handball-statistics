<?php
/*
 * Input variables:
 *  - action: requested action, mandatory
 *    - get
 *    - register
 *  - usr: user name.
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
 *     11: not registered
 *     20: DB error
 *
 * Available actions:
 *  - register: register device data
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

extract($_GET, EXTR_PREFIX_ALL, "var");
extract($_POST, EXTR_PREFIX_ALL, "var");

// Connect to the database
require_once("db_handler.php");
require_once("db_credentials_handball.php");
$db = db_connect(DB_TYPE, DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD);
if ($db == NULL) die("No se pudo conectar con la base de datos");
db_set_charset($db, "utf8");

$error = 0;
$data = array();
$html = "";

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
			$error = 2;
		} else if (!isset($var_pwd)) {
			$error = 3;
		} else {
			if (($id = db_users_get_id($var_usr)) < 0) {
				if (devices_register($var_email, $var_address)) {
					if (($id = devices_get_id($var_email, $var_address)) >= 0) {
						if (devices_update($id, $var_email, $var_address, 
							$var_brand, $var_manufacturer, $var_device, $var_model, $var_product, 
							$var_country_code, $var_language_code)) {
								if (devices_set_token($id, mt_rand()) && send_mail_pear($id)) {
									$error = 0;
								} else $error = 20;
						} else $error = 20;
					} else $error = 20;
				} else $error = 20;
			} else {
				if (devices_update($id, $var_email, $var_address, 
					$var_brand, $var_manufacturer, $var_device, $var_model, $var_product, 
					$var_country_code, $var_language_code)) {
						if (devices_set_token($id, mt_rand()) && send_mail_pear($id)) {
							$error = 0;
						} else $error = 20;
				} else $error = 20;
			}
		}
		break;
	default:
		$error = 1;
		break;
}
?>
