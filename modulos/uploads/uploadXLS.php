<?php
require_once '../../includes/excel/reader.php';

// funcion que carga del archivo Excel los codigos a actualizar, solo para tipo XLS
function uploadXLS($nombre_archivo){
$data = new Spreadsheet_Excel_Reader();
$data->setOutputEncoding('CP1251');
$data->read(DIR_UPLOAD.$nombre_archivo);

error_reporting(E_ALL ^ E_NOTICE);

	for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
		for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
			$datos = $data->sheets[0]['cells'][$i][$j].",".$datos;
		}
	}

	return $datos;
}
?>