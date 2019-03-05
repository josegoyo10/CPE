<?php
function BARCODE_EAN13($number, $width, $height) {
    
	$basePath = "ui/view/theme/images/barcode/";
	$imageHeight = $height*85/100;
	$imageHeight2 = $height*15/100;
	$imageWidth = $width/95*7;
	$imageWidth2 = $width/95*3;
	$imageWidth3 = $width/95*5;

	if (strlen($number)>13) {
		return "<b>Error</b>";
	}
    
	$orientation = array (
        '000000', '001011', '001101', '001110', '010011',
        '011001', '011100', '010101', '010110', '011010');

    $strPad = sprintf("%013s", $number);
	$startDigit = substr($strPad, 0, 1);
	$leftPart = substr($strPad, 1, 6);
	$rightPart = substr($strPad, 7, 6);

	$ret .= "<table border='0' cellspacing='0' cellpadding='0'>";
	$ret .= "<tr>";

	$ret .= "<td>";
	$ret .= "&nbsp;";
	$ret .= "</td>";

	$ret .= "<td>";
	$ret .= "<img src=\"".$basePath."c1-c3.png\" height=\"$imageHeight\" width=\"$imageWidth2\">";
	$ret .= "</td>";

	for ($i=0; $i<6; ++$i) { //Para el tramo izquierda
		$ret .= "<td>";
		$ret .= "<img src=\"".$basePath.((!substr($orientation[$startDigit], $i, 1))?substr($leftPart, $i, 1)."-der-neg.png":substr($leftPart, $i, 1)."-izq.png") . "\" height=\"$imageHeight\" width=\"$imageWidth\">";
		$ret .= "</td>";
	}

	$ret .= "<td>";
	$ret .= "<img src=\"".$basePath."c2.png\" height=\"$imageHeight\" width=\"$imageWidth3\">";
	$ret .= "</td>";

	for ($i=0; $i<6; ++$i) { //Para el tramo derecha
		$ret .= "<td>";
		$ret .= "<img src=\"".$basePath.substr($rightPart, $i, 1)."-der.png\" height=\"$imageHeight\" width=\"$imageWidth\">";
		$ret .= "</td>";
	}

	$ret .= "<td>";
	$ret .= "<img src=\"".$basePath."c1-c3.png\" height=\"$imageHeight\" width=\"$imageWidth2\">";
	$ret .= "</td>";

	$ret .= "</tr>";
	$ret .= "<tr>";

	$ret .= "<td>";
	$ret .= $startDigit . "&nbsp;";;
	$ret .= "</td>";

	$ret .= "<td valign=top>";
	$ret .= "<img src=\"".$basePath."c1-c3.png\" height=\"$imageHeight2\" width=\"$imageWidth2\">";
	$ret .= "</td>";

	for ($i=0; $i<6; ++$i) { //Para el tramo izquierda
		$ret .= "<td align=center>";
		$ret .= substr($leftPart, $i, 1);
		$ret .= "</td>";
	}

	$ret .= "<td valign=top>";
	$ret .= "<img src=\"".$basePath."c2.png\" height=\"$imageHeight2\" width=\"$imageWidth3\">";
	$ret .= "</td>";

	for ($i=0; $i<6; ++$i) { //Para el tramo derecha
		$ret .= "<td align=center>";
		$ret .= substr($rightPart, $i, 1);
		$ret .= "</td>";
	}

	$ret .= "<td valign=top>";
	$ret .= "<img src=\"".$basePath."c1-c3.png\" height=\"$imageHeight2\" width=\"$imageWidth2\">";
	$ret .= "</td>";

	$ret .= "</tr>";
	$ret .= "</table>";
	return $ret;
}
?>