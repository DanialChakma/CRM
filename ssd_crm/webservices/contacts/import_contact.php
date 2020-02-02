<?php

/**
 * Created by PhpStorm.
 * User: Md. Mahamudul Hasan Khan
 * Date: 9/10/2015
 * Time: 12:55 PM
 */
ini_set('memory_limit', '7192M');
ini_set('max_execution_time', 900);
include_once "../lib/common.php";
checkSession();
$user_id = $_SESSION['user_id'];
$cn = connectDB();
$valueIndex = json_decode($_REQUEST['value_index']);
$phoneValidation = json_decode($_REQUEST['phone_validation']);
//print_r($valueIndex);
//foreach ($valueIndex as $value) {
//    print_r($value);
//}
$empty_data=0;
$arrayResult = array();

$fileArray = explode(".", $_FILES["uploadFile"]["name"]);
$fileType = $fileArray[sizeof($fileArray) - 1];

//print_r($_REQUEST);

if ($fileType == 'xlsx' || $fileType == 'xls' || $fileType == 'csv' || $fileType == 'txt') {

    $tempFileName = $_FILES["uploadFile"]["tmp_name"];
    //    $filename = "upload/" . $_FILES["uploadFile"]["name"] . '_' . time();
    $filename = 'a.xls';
    move_uploaded_file($tempFileName, $filename);

    //excel reader starts
    require_once '../PHPExcel/PHPExcel/IOFactory.php';
    $inputFileType = PHPExcel_IOFactory::identify($filename);

//    /**  PHPExcel Object  * */

    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objReader->setReadDataOnly(true);

    $objPHPExcel = $objReader->load($filename);

    $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);


    $highestRow = $objWorksheet->getHighestRow();
    $highestColumn = $objWorksheet->getHighestColumn();
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

    $index = 0;
    $flag = 0;
    $total = 0;
    $err = 0;
    $duplicate_no = '';
    $total_duplicate = 0;
    $duplicate_check = array();

    $sheet = $objPHPExcel->getSheet(0);
    $highestRow = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();

    // This loop for getting the phone number from the contact_details table to check duplicacy in database.
    foreach ($valueIndex as $key => $value) {
        if ($key == $_REQUEST['duplicate']) {
            $duplicate_check_sql_column = $value;
            break;
        }
    }


    $duplicate_qry = "select " . $duplicate_check_sql_column . " from contacts";
    $result = Sql_exec($cn, $duplicate_qry);
    $num_total = Sql_Num_Rows($result);
    for ($i = 1; $i <= $num_total; $i++) {
        $row = sql_fetch_array($result);
        $duplicate_check[(string) trim($row["$duplicate_check_sql_column"])] = 1;
    }
    // print_r($duplicate_check);
    Sql_Free_Result($result);


    $duplicate_data = 0;
    $inserted_data = 0;
    $divide = ceil($highestRow / 20);

    $start = 1;
    $end = $divide;
    $last_data = 0;
    for ($k = 1; $k <= 20; $k++) {
        if($empty_data == 10)
            break;
        $contact_sql = "INSERT INTO contacts (";
        foreach ($valueIndex as $key => $value) {
            if ($key > 0) {
                $contact_sql = $contact_sql . "," . $value;
            } else {
                $contact_sql = $contact_sql . $value;
            }
        }
        $contact_sql = $contact_sql . ") VALUES	";
        $rowData = $sheet->rangeToArray('A' . $start . ':' . $highestColumn . $end, NULL, TRUE, FALSE);
        $start = $end;
        $end = $end + $divide;
        for ($i = 1; $i < count($rowData); $i++) {
            if($rowData[$i][$phoneValidation]== NULL && $rowData[$i-1][$phoneValidation]==NULL)
            {
                $empty_data++;
                //echo $empty_data." ";
            }
            else{
                $empty_data = 0;
                //echo $empty_data." ".$rowData[$i][$phoneValidation]." ";
            }
            if($empty_data == 10)
                break;
            $lead_contact = $rowData[$i][$phoneValidation];
            //phone validation starts
            if (strlen($lead_contact) > 5) {
                $phone_no = trim($lead_contact);
                $phone_no = (string) $phone_no;
                if (is_numeric($phone_no)) {
                    $numberPrefix = substr($phone_no, 0, 1);
                    if ($numberPrefix == '1') {
                        $phone_no = '0' . $phone_no;
                    }
                    $numberLength = strlen($phone_no);
                    $numberPrefix = substr($phone_no, 0, 3);
                    if ($numberLength == 11) {
                        // receiver number
                        if ($numberPrefix != '017' &&
                                $numberPrefix != '018' &&
                                $numberPrefix != '019' &&
                                $numberPrefix != '016' &&
                                $numberPrefix != '015' &&
                                $numberPrefix != '011'
                        ) {
                            $flag = false;
                            // return false; // 'WRONG PHONE NUMBER';
                        } else {
                            // return $phone_no;
                            $flag = true;
                        }
                    } else if ($numberLength == 6)
                        $flag = true;
                    else {
                        $flag = false;
                        // return false;
                    }
                }
            }
            //validation ends
            if (is_numeric($lead_contact))
                if ($flag) {
                    $last_data = 1;
                    $rowData[$i][$phoneValidation] = $lead_contact;
                    if (!isset($duplicate_check[$rowData[$i][$_REQUEST['duplicate']]])) {
                        $contact_sql = $contact_sql . "(";
                        $rowData[$i][$phoneValidation] = $lead_contact;
                        for ($j = 0; $j < count($rowData[$i]); $j++) {

                            $contact_sql = $contact_sql . "'" . addslashes($rowData[$i][$j]) . "'";
                            if ($j + 1 != count($rowData[$i]))
                                $contact_sql = $contact_sql . ",";
                        }
                        $contact_sql = $contact_sql . "),";
                        $inserted_data++;
                        //echo $contact_sql."\n\n";
                    } else {
                        $duplicate_data++;
                        $duplicate_no = $duplicate_no . $rowData[$i][$_REQUEST['duplicate']] . " ";
                    }
                } else
                    $last_data = 0;
        }
        if ($contact_sql[strlen($contact_sql) - 1] == ',') {
            $contact_sql[strlen($contact_sql) - 1] = " ";
        }
        
        //echo $contact_sql;
        if ($inserted_data != 0) {
            //echo $contact_sql." ".$empty_data." ".is_null($rowData[$i][$phoneValidation])." ";
            $result = Sql_exec($cn, $contact_sql);
            Sql_Free_Result($result);
        }
        unset($rowData);
        unset($contact_sql);
        //echo $contact_sql."\n\n";
    }


    //echo $duplicate_data . " " . $inserted_data;
    if ($duplicate_data != 0) {

        $return_data = array('status' => true, 'message' => '<span style="color:green;"> Total <b>' . $inserted_data . '</b> Contacts Successfully inserted.</span> and <span  style="color:red;"><b>' . $duplicate_data . '</b> Contact No is duplicated  <b>(' . $duplicate_no . ')</b></span>');
    } else if (strlen($duplicate_data) == 0 && $inserted_data != 0) {
        $return_data = array('status' => true, 'message' => '<span style="color:green;"> Total <b>' . $inserted_data . '</b> Contacts Successfully inserted.</span>');
    } else {

        $return_data = array('status' => false, 'message' => 'Failed : file format error!!!');
    }
    echo json_encode($return_data);
}
