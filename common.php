<?php
function common_is_email_format($email) {
	$ret = false;
	if (preg_match("/\w+@\w+\.\w+/", $email)) $ret = true;
	if (strlen($email) > 255) $ret = false;
	return $ret;
}

function common_is_password_format($password) {
	$ret = true;
	if (strlen($password) < 6) $ret = false;
	return $ret;
}

//	function common_get_html($str) {
//				return htmlentities($str, ENT_QUOTES | ENT_IGNORE, "UTF-8");
//					}
//
//	function common_has_mac_format($mac) {
//				$ret = true;
//						if (strlen($mac) != 17) $ret = false;
//						$index_array = array(2,5,8,11,14);
//								foreach ($index_array as $i) {
//												if ($mac[$i] != ":") $ret = false;
//														}
//								$index_array = array(0,1,3,4,6,7,9,10,12,13,15,16);
//								foreach ($index_array as $i) {
//												if (!((($mac[$i] >= "0") && ($mac[$i] <= "9")) || (($mac[$i] >= "a") && ($mac[$i] <= "f")) || (($mac[$i] >= "A") && ($mac[$i] <= "F")))) $ret = false;
//														}
//										return $ret;
//									}
?>

