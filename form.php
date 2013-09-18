<?php

function form_label($text, $id) {
	$str = "<label class='control-label'";
	if (isset($id)) $str .= " for='input_$id'";
	$str .= ">$text</label> \n";
	return $str;
}

function form_input_text($type, $placeholder, $name, $value) {
	$str = "<input type='$type'";
	if (isset($placeholder)) $str = $str." placeholder='$placeholder'";
	if (isset($name)) $str = $str." name='$name'";
	if (isset($name)) $str = $str." id='input_$name'";
	if (isset($value)) $str = $str." value='$value'";
	$str = $str." > \n";
	return $str;
}

function form_help($text) {
	$str = "<span class='help-inline'>$text</span> \n";
	return $str;
}

function form_selector($data, $name, $default) {
	$str = "<select name='$name' size='1'> \n";
	if (is_array($data)) {
		foreach ($data as $row) {
			if (isset($default) && ($default == $row['id.team'])) $selected = "selected";
			else $selected = "";
			$value = common_get_html($row['name.team']);
			$str .= "<option value='{$row['id.team']}' $selected>$value</option> \n";
		}
	}
	$str .= "</select> \n";
	return $str;
}

function form_row($label, $input) {
	$str = "<div class='control-group'> \n";
	$str .= $label;
	$str .= "<div class='controls controls-row'> \n";
	$str .= $input;
	$str .= "</div> \n";
	$str .= "</div> \n";
	return $str;
}

function form_1label_1input_text($tag, $name, $value, $help) {
	$label = form_label($tag, $name);
	$input = form_input_text("text", $tag, $name, $value);
	$input .= $help;
	return form_row($label, $input);
}

function form_1label_2input_text($tag, $name1, $value1, $name2, $value2, $help) {
	require_once("common.php");
	$tag = common_get_html($tag);
	$label = form_label($tag);
	$input1 = form_input_text("text", $tag, $name1, $value1);
	$input2 = form_input_text("text", $tag, $name2, $value2);
	return form_row($label, $input1.$input2.$help);
}

?>
