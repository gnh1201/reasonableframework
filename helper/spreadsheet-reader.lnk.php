<?php
/**
  * @file spreadsheet-reader.lnk.php
  * @date 2018-09-20
  * @author Go Namhyeon <gnh1201@gmail.com>
  * @brief Excel file parser
***/

loadVendor(array(
	"spreadsheet-reader/php-excel-reader/excel_reader2",
	"spreadsheet-reader/SpreadsheetReader",
	"spreadsheet-reader/SpreadsheetReader_CSV",
	"spreadsheet-reader/SpreadsheetReader_XLSX",
	"spreadsheet-reader/SpreadsheetReader_XLS",
	"spreadsheet-reader/SpreadsheetReader_ODS"
));

if(!function_exists("parse_excel_file")) {
	function parse_excel_file($filepath) {
		
	}
}
