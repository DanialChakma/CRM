<?php

/**
 * Created by PhpStorm.
 * User: Md. Mahamudul Hasan Khan
 * Date: 9/10/2015
 * Time: 12:55 PM
 */
ini_set('memory_limit', '4024M');
ini_set('max_execution_time', 900);
include_once "../lib/common.php";
checkSession();


$cn = connectDB();
$arrayResult = array();

$fileArray = explode(".", $_FILES["uploadFile"]["name"]);
$fileType = $fileArray[sizeof($fileArray) - 1];




if ($fileType == 'xlsx' || $fileType == 'xls' || $fileType == 'csv' || $fileType == 'txt') {

    $tempFileName = $_FILES["uploadFile"]["tmp_name"];
//    $filename = "upload/" . $_FILES["uploadFile"]["name"] . '_' . time();
    $filename = 'a.xls';
    move_uploaded_file($tempFileName, $filename);

    //excel reader starts
    require_once '../PHPExcel/PHPExcel/IOFactory.php';
    $inputFileType = PHPExcel_IOFactory::identify($filename);

    /**  PHPExcel Object  * */
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objReader->setReadDataOnly(true);

    $objPHPExcel = $objReader->load($filename);

    $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);

    $rowData = array();
    $highestColumn = $objWorksheet->getHighestColumn();
    $highestRow = $objWorksheet->getHighestRow();
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
    $query = "describe contacts";
    $i = 0;
    $options ="";
    $result = Sql_exec($cn, $query);
    while ($dt = Sql_fetch_array(($result))) {
        if ($dt['Field'] != 'id') {
            $optionsval = $dt['Field'];
            $optionsname = ucwords(str_replace("_", " ", $optionsval));
            $options = $options. '<option value= "' . $optionsval . '">' . $optionsname . '</option>';
            $i++;
        }
    }

    //print_r($options);
    $sheet = $objPHPExcel->getSheet(0);
    $highestColumn = $sheet->getHighestColumn();

//  Loop through each row of the worksheet in turn
    //  Read a row of data into an array
    $data = array();
    $duplicate_option= "";
    $rowData['excel-column'] = $sheet->rangeToArray('A' . 1 . ':' . $highestColumn . 1, NULL, TRUE, FALSE);
    for($i=0;$i<count($rowData['excel-column'][0]);$i++)
    {
        $duplicate_option = $duplicate_option.'<option value= "' . $i . '">' . $rowData['excel-column'][0][$i] . '</option>';
    }
    $rowData['sql_column'] = $options;
    $rowData['duplicate_option'] = $duplicate_option;
    $rowData['data'] = $sheet->rangeToArray('A' . 2 . ':' . $highestColumn . 2, NULL, TRUE, FALSE);
   // for($i=0;$i<$highestRow;$i++){
       // $data= $sheet->rangeToArray('A' . 2 . ':' . $highestColumn . $highestRow, NULL, TRUE, FALSE);
    //}
    //$rowData['alldata'] = $data;
//  Insert row data array into your database of choice here
    ClosedDBConnection($cn);
    echo json_encode($rowData);
}