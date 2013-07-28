<?php
	/*
	 * Input variables:
	 *  - action: requested action, mandatory
	 *    * register
	 *    * validate
	 *    * get
	 *    * delete 
	 *    * check
	 * - address: device mac address.
	 * - email: user e-mail address.
	 * - token: passphrase.
	 * - brand: Android param. Mandatory parameter for 'check' action
	 * - manufacturer: Android param. Mandatory parameter for 'check' action
	 * - device: Android param. Mandatory parameter for 'check' action
	 * - model: Android param. Mandatory parameter for 'check' action
	 * - product: Android param. Mandatory parameter for 'check' action
	 * - country_code: Android param.
	 * - language_code: Android param.
	 *
	 * Returned values:
	 *  - XML formated data
	 *    <?xml version="1.0" encoding="utf-8"?>
	 *    <devices>
	 *    <code>ErrorCode</code>
	 *    <data>ReturnedData</data>
	 *    </devices>
	 *
	 *    ErrorCode:
	 *     0: success
	 *     1: invalid action
	 *     2: invalid address
	 *     3: invalid email
	 *     4: invalid token
	 *     10: already registered
	 *     11: not registered
	 *     12: already validated
	 *     20: DB error
	 *     30: missing parameter
	 *     31: Incoming token does not match with the expected one
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

	/* Return functions */
	function write_header() {
		header('Content-type: application/xml');
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		echo "<devices>\n";
	}

	function write_footer() {
		echo "</devices>";
	}

	function write_code($code) {
		echo "<code>" . $code . "</code>\n";
	}

	function write_xml_data_field($data, $field) {
		if (isset($data[$field]))
			echo "<$field>" . $data[$field] . "</$field>\n";
	}

	function write_data($data) {
		echo "<data>\n";
		write_xml_data_field($data, "id");
		write_xml_data_field($data, "email");
		write_xml_data_field($data, "mac");
		write_xml_data_field($data, "type");
		write_xml_data_field($data, "brand");
		write_xml_data_field($data, "manufacturer");
		write_xml_data_field($data, "device");
		write_xml_data_field($data, "model");
		write_xml_data_field($data, "product");
		write_xml_data_field($data, "country_code");
		write_xml_data_field($data, "language_code");
		write_xml_data_field($data, "verified");
		write_xml_data_field($data, "created_at");
		echo "</data>\n";
	}

	function send_mail($id, $mac, $email) {
		$token = devices_get_token($id);
		$url_devices = $_SERVER['SCRIPT_URI'];
		$link_ok = $url_devices."?action=validate&address=$mac&email=$email&token=$token&format=html";
		$link_err = $url_devices."?action=delete&address=$mac&email=$email&token=$token&format=html";
		$subject="Registro de producto en la Plataforma Blusens";
		$message="<html><body>Estimado usuario:<br><br>Para completar el registro de tu nuevo producto Blusens, por favor haz click <a href='$link_ok'>aqu&iacute;</a><br>Si no esperabas recibir este correo, haz clic <a href='$link_err'>aqu&iacute;</a></body></html>";
		$headers = "From: donotreply@blusens.com\r\n";
		$headers .= "Content-type: text/html\r\n";
		return mail($email,$subject,$message,$headers); 
	}

	function send_mail_pear($id) {
		require_once "Mail.php";
		require_once "Mail/mime.php";

		$entry = db_handler_devices_get($id);
		$mac = $entry['mac'];
		$email = $entry['email'];

		$token = devices_get_token($id);
		$url_devices = $_SERVER['SCRIPT_URI'];
		$link_validate = $url_devices."?action=validate&address=$mac&email=$email&token=$token&format=html";
		$link_unsuscribe = $url_devices."?action=delete&address=$mac&email=$email&token=$token&format=html";
		$from = "Blusens <noreply@blusens.com>";
		$to = "<$email>";
		$subject = "Registro de producto Blusens";

		$body_plain = "Estimado usuario:\n\nPara completar el registro de tu nuevo producto Blusens, por favor accede al siguiente enlace:\n$link_validate\n\nSi no esperabas recibir este correo, accede al siguiente enlace:\n$link_unsuscribe\n\nBlusens.\n";

		//$body_html = "<html><body>Estimado usuario:<br><br>Para completar el registro de tu nuevo producto Blusens, por favor haz click <a href='$link_validate'>aqu&iacute;</a><br>Si no esperabas recibir este correo, haz clic <a href='$link_unsuscribe'>aqu&iacute;</a><br></body></html>";
		$body_message = db_handler_get_email_text("message", $entry['country_code']);
		$body_message = str_replace("@@link_validate@@", $link_validate, $body_message);
		$body_message = str_replace("@@link_unsuscribe@@", $link_unsuscribe, $body_message);
		$body_html = $body_message."<br>".get_html_foot($id);

		$crlf = "\n";

		$headers['From'] = $from;
		$headers['To'] = $to;
		$headers['Subject'] = $subject;

		$mime = new Mail_mime(array('eol' => $crlf));
		$mime->addHTMLImage("./img/blusens_logo.png", "image/png");
		$mime->setTXTBody($body_plain);
		$mime->setHTMLBody($body_html);

		$body = $mime->get();
		$headers = $mime->headers($headers);

		$smtp_info['host'] = "smtp.mundo-r.com";
		$smtp_info['auth'] = true;
		$smtp_info['username'] = "developers@blusens.com";
		$smtp_info['password'] = "12qwerty90";

		$smtp = Mail::factory('smtp', $smtp_info); 
		$mail = $smtp->send($to, $headers, $body);

		if (PEAR::isError($mail)) {
			return false;
		} else {
			return true;
		}
	}

	function get_html_foot($id) {
		$html_foot = "";
		$entry = db_handler_devices_get($id);
		$body_signature = "<br><br><br><a href='http://www.blusens.com' target='_blank'><img src='./img/blusens_logo.png'/></a><br><br>";
		$body_banner = "<img src='".db_handler_get_email_text("banner", $entry['country_code'])."'/>";
		$html_foot = $body_signature."<br>".$body_banner;
		return $html_foot;
	}

	import_request_variables("GP", "var_");

	require_once("db_handler.php");
	require_once("db_credentials_register.php");
	db_handler_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

	require_once("common.php");

	$code = 0;
	$data = array();
	$html = "";

	switch ($var_action) {
		case "register":
			if ((isset($var_address) == false) || !common_has_mac_format($var_address)) {
				$code = 2;
			} else if ((isset($var_email) == false) || !common_has_email_format($var_email)) {
				$code = 3;
			} else {
				if (($id = devices_get_id($var_email, $var_address)) < 0) {
					if (devices_register($var_email, $var_address)) {
						if (($id = devices_get_id($var_email, $var_address)) >= 0) {
							if (devices_update($id, $var_email, $var_address, 
								$var_brand, $var_manufacturer, $var_device, $var_model, $var_product, 
								$var_country_code, $var_language_code)) {
									if (devices_set_token($id, mt_rand()) && send_mail_pear($id)) {
										$code = 0;
									} else $code = 20;
							} else $code = 20;
						} else $code = 20;
					} else $code = 20;
				} else {
					if (devices_update($id, $var_email, $var_address, 
						$var_brand, $var_manufacturer, $var_device, $var_model, $var_product, 
						$var_country_code, $var_language_code)) {
							if (devices_set_token($id, mt_rand()) && send_mail_pear($id)) {
								$code = 0;
							} else $code = 20;
					} else $code = 20;
				}
			}
			break;
		case "validate":
			if (isset($var_address) == false) {
				$code = 2;
			} else if (isset($var_email) == false) {
				$code = 3;
			} else if (isset($var_token) == false) {
				$code = 4;
			} else {
				$id = devices_get_id($var_email, $var_address);
				if ($id < 0) {
					$code = 11;
				} else if (devices_get_token($id) != $var_token) {
					$code = 31;
				} else if (devices_is_validated($id) == true) {
					$code = 12;
				} else if (devices_set_verified($id) == false) {
					$code = 20;
				} else {
					$entry = db_handler_devices_get($id);
					$body_message = db_handler_get_email_text("validate", $entry['country_code']);
					$html = "<html><body>".$body_message."<br>".get_html_foot($id)."</body></html>";
				}
			}
			break;
		case "delete":
			if (isset($var_address) == false) {
				$code = 2;
			} else if (isset($var_email) == false) {
				$code = 3;
			} else if (isset($var_token) == false) {
				$code = 4;
			} else {
				$id = devices_get_id($var_email, $var_address);
				if ($id < 0) {
					$code = 11;
				} else if (devices_get_token($id) != $var_token) {
					$code = 31;
				} else if (devices_delete_email($id) == false) {
					$code = 20;
				} else {
					$entry = db_handler_devices_get($id);
					$body_message = db_handler_get_email_text("unsuscribe", $entry['country_code']);
					$html = "<html><body>".$body_message."<br>".get_html_foot($id)."</body></html>";
				}
			}
			break;
		case "get":
			if ((isset($var_address) == false) || !common_has_mac_format($var_address)) {
				$code = 2;
			} else if ((isset($var_email) == false) || !common_has_email_format($var_email)) {
				$code = 3;
			} else {
				$id = devices_get_id($var_email, $var_address);
				if ($id < 0) {
					$code = 11;
				} else {
					$data= db_handler_devices_get($id);
					//$data['vendor'] = devices_get_vendor($data['mac']);
				}
			}
			break;
		case "check":
			if (!isset($var_brand) || !isset($var_manufacturer) || !isset($var_device) || !isset($var_model) || !isset($var_product)) {
				$code = 30;
			} else {
				if (!devices_build_is_registered($var_brand, $var_manufacturer, $var_device, $var_model, $var_product)) {
					$code = 11;
				} else {
					$ret = devices_build_get($var_brand, $var_manufacturer, $var_device, $var_model, $var_product);
					$data['type'] = $ret['type'];
				}
			}
			break;
		default:
			$code = 1;
			break;
	}

	/* Write response */
	if (isset($var_format) && ($var_format == "html")) {
		header('Content-type: text/html');
		if (!$code) {
			echo $html;
		} else {
			echo "<html><body>Se ha producido un error (C&oacute;digo $code)</body></html>";
		}
	} else {
		write_header();
		write_code($code);
		write_data($data);
		write_footer();
	}
?>
