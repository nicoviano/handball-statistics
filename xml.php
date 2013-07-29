<?php

function xml_set_content_type() {
	header('Content-type: application/xml');
}

function xml_write_header() {
	echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
}

function xml_write_tag_start($tag) {
	echo "<$tag>\n";
}

function xml_write_tag_end($tag) {
	echo "</$tag>\n";
}

function xml_write_tag_value($tag, $value) {
	echo "<$tag>$value</$tag>\n";
}

function xml_write_xml_data_field($data, $field) {
	if (isset($data[$field]))
		echo "<$field>" . $data[$field] . "</$field>\n";
}

function xml_write_data($data) {
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

?>
