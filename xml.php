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

function xml_encode($mixed, $domElement=null, $DOMDocument=null) {
    if (is_null($DOMDocument)) {
        $DOMDocument = new DOMDocument('1.0', 'utf-8');
        $DOMDocument->formatOutput = true;
        xml_encode($mixed, $DOMDocument, $DOMDocument);
        return $DOMDocument->saveXML();
    } else {
        if (is_array($mixed)) {
            foreach ($mixed as $index => $mixedElement) {
                if (is_int($index) && (is_array($mixedElement))) {
                    if ($index == 0) {
                        $node = $domElement;
                    } else {
                        $node = $DOMDocument->createElement($domElement->tagName);
                        $domElement->parentNode->appendChild($node);
                    }
                } else {
                    $plural = $DOMDocument->createElement($index);
                    $domElement->appendChild($plural);
                    $node = $plural;
                    if ((rtrim($index,'s') !== $index) && (is_array($mixedElement))) {
                        $singular = $DOMDocument->createElement(rtrim($index, 's'));
                        $plural->appendChild($singular);
                        $node = $singular;
                    }
                }
                xml_encode($mixedElement, $node, $DOMDocument);
            }
        } else {
		if (is_int($mixed)) {
			$value = "$mixed";
		} else if (is_float($mixed)) {
			$value = number_format($mixed, 2);
		} else {
			$value = $mixed;
		}
		$domElement->appendChild($DOMDocument->createTextNode($value));
        }
    }
}


?>
