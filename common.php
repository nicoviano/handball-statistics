<?php

function common_is_email_format($email) {
	$ret = false;
	if ($res=preg_match("/\w+@\w+\.\w+/", $email)) $ret = true;
	if (strlen($email) > 255) $ret = false;
	return $ret;
}

function common_is_password_format($password) {
	$ret = true;
	if (strlen($password) < 6) $ret = false;
	return $ret;
}

function common_is_time_date_format($time_date) {
	$ret = false;
	if (preg_match("/^(19|20)\d\d[-\/](0[1-9]|1[012])[-\/](0[1-9]|[12][0-9]|3[01]).([01][0-9]|2[0123]):[0-5][0-9]$/", $time_date)) {
		$ret = true;
	}
	return $ret;
}


function common_get_html($str) {
	return htmlentities($str, ENT_QUOTES | ENT_IGNORE, "UTF-8");
}

?>
