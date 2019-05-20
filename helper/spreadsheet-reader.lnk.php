<?php
/**
  * @file spreadsheet-reader.lnk.php
  * @date 2018-09-20
  * @author Go Namhyeon <gnh1201@gmail.com>
  * @brief Excel file parser
***/

if(!check_function_exists("parse_excel_file")) {
    function parse_excel_file($filepath, $format="xlsx", $setColumnName=false) {
        $rows = array();

        $required_files = array(
            "spreadsheet-reader/php-excel-reader/excel_reader2",
            "spreadsheet-reader/SpreadsheetReader",
            //"spreadsheet-reader/SpreadsheetReader_CSV",
            //"spreadsheet-reader/SpreadsheetReader_XLSX",
            //"spreadsheet-reader/SpreadsheetReader_XLS",
            //"spreadsheet-reader/SpreadsheetReader_ODS"
        );
        foreach($required_files as $file) {
            include("./vendor/_dist/" . $file . ".php");
        }

        $spreadsheet = false;
        $columnNames = array();
        $fileFormat = strtolower($format);

        /*
        if($fileFormat == "xlsx") {
            $spreadsheet = new SpreadsheetReader_XLSX($filepath);
        } elseif($fileFormat == "xls") {
            $spreadsheet = new SpreadsheetReader_XLS($filepath);
        } elseif($fileFormat == "csv") {
            $spreadsheet = new SpreadsheetReader_CSV($filepath);
        } elseif($fileFormat == "ods") {
            $spreadsheet = new SpreadsheetReader_ODS($filepath);
        } else {
            $spreadsheet = new SpreadsheetReader($filepath);
        }
        */
        $spreadsheet = new SpreadsheetReader($filepath);

        foreach($spreadsheet as $index=>$row) {
            if(!$setColumnName) {
                $rows[] = $row;
            } else {
                if($index > 0) {
                    $i = 0;
                    $cols = array();
                    foreach($row as $col) {
                        if((count($columnNames) - 1) > $i) {
                            $cols[$columnNames[$i]] = $col;
                        } else {
                            $cols[] = $col;
                        }
                        $i++;
                    }
                    $rows[] = $cols;
                } else {
                    $columnNames = array_merge($columnNames, $row);
                }
            }
        }

        return $rows;
    }
}
