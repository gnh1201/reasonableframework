<?php
/**
  * @file spreadsheet-reader.lnk.php
  * @date 2018-09-20
  * @author Go Namhyeon <gnh1201@gmail.com>
  * @brief Excel file parser
***/

// load spreadsheet reader library
loadVendor(array(
	"spreadsheet-reader/php-excel-reader/excel_reader2",
	"spreadsheet-reader/SpreadsheetReader",
	"spreadsheet-reader/SpreadsheetReader_CSV",
	"spreadsheet-reader/SpreadsheetReader_XLSX",
	"spreadsheet-reader/SpreadsheetReader_XLS",
	"spreadsheet-reader/SpreadsheetReader_ODS"
));

if(!function_exists("parse_excel_file")) {
	function parse_excel_file($filepath, $format="xlsx") {
		$rows = array();
		
		$spreadsheet = false;
		$fileformat = strtolower($format);
		
		if($fileformat == "xlsx") {
			$spreadsheet = new SpreadsheetReader_XLSX($filepath);
		} elseif($fileformat == "xls") {
			$spreadsheet = new SpreadsheetReader_XLS($filepath);
		} elseif($fileformat == "csv") {
			$spreadsheet = new SpreadsheetReader_CSV($filepath);
		} elseif($fileformat == "ods") {
			$spreadsheet = new SpreadsheetReader_ODS($filepath);
		} else {
			$spreadsheet = new SpreadsheetReader($filepath);
		}
		
		
		
	}
}
