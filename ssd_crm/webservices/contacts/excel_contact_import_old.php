<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 12/15/2015
 * Time: 3:55 PM
 */

ini_set('memory_limit', '4024M');
ini_set('max_execution_time', 900);
include_once "../lib/common.php";
checkSession();
$user_id = $_SESSION['user_id'];
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

    $query = "SELECT * FROM contacts";
    $result = Sql_exec($cn, $query);
    $num_total = Sql_Num_Rows($result);
    // This loop for getting the phone number from the contact_details table to check duplicacy in database.
    for ($i = 1; $i <= $num_total; $i++) {
        $row = sql_fetch_array($result);
        $duplicate_check[(string) trim($row['phone1'])] = 1; //$duplicate_check contains 1 mapping phone1 number.
        $duplicate_check[(string) trim($row['phone2'])] = 1; //$duplicate_check contains 1 mapping phone2 number.
    }

    Sql_Free_Result($result);

    $contact_sql = "INSERT INTO contacts (lead_source, customer_type, create_date, first_name, last_name, email, phone1, address1, note)	VALUES	";
//    $contacts_details_sql = "INSERT INTO  contact_details	( contact_id, 	first_name, 	last_name, 	email, 	phone1,  	address1,  	note	)	VALUES	";
    $conversions_history_sql = "INSERT INTO customer_conversion 	( contact_id,  	conversion_status, conversion_note, client_type ) 	VALUES ";
    $contacts = array();
//    $contacts_details = array();
    $conversions_history = array();
    $contacts_value[] = array();
//    $contacts_details_value[] = array();
    $conversions_history_value[] = array();

    $count = 0;
    $check_null_in_excel = 0; // $check_null_in_excel  variable is as to identify consecutive 10 null rows in excell;
    $excel_slice_no = ceil($highestRow / 10); // $excel_slice_no is use to insert into database excel rows at 10 times.

    $ro = 0;
    for ($k = 1; $k <= 10; $k++) {

        $contacts = array();
//        $contacts_details = array();
        $conversions_history = array();
        $contacts_value[] = array();
//        $contacts_details_value[] = array();
        $conversions_history_value[] = array();

        for ($r = 1; $r <= $excel_slice_no; ++$r) {
            if ($check_null_in_excel == 1)
                break;
            $ro++;
            $lead_contact = $objWorksheet->getCellByColumnAndRow(1, $ro)->getValue();
            if (strlen($lead_contact) == 0) {
                $count++;
                if ($count > 10)
                    $check_null_in_excel = 1;
            }
            //  echo $lead_contact.'|'.$row.' .. ';


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
                    if ($flag) {
                        $contact_id = 0;
                        $contact_id = $duplicate_check[$phone_no];
                        $duplicate_check[$phone_no] = 1;

                        if ( $contact_id == 0 ) {
                            $contact_id = $phone_no;


                            // reading the unique rows from excel for insertion into database.
                            $lead_source = $objWorksheet->getCellByColumnAndRow(0, $ro)->getValue();
                            $lead_source = addslashes($lead_source);
                            $lead_name = $objWorksheet->getCellByColumnAndRow(2, $ro)->getValue();
                            $lead_name = addslashes($lead_name);
                            $lead_location = $objWorksheet->getCellByColumnAndRow(3, $ro)->getValue();
                            $lead_location = addslashes($lead_location);
                            $lead_email = $objWorksheet->getCellByColumnAndRow(4, $ro)->getValue();
                            $lead_email = addslashes($lead_email);
                            $lead_status = $objWorksheet->getCellByColumnAndRow(5, $ro)->getValue();
                            $lead_status = addslashes($lead_status);
                            $lead_notes = $objWorksheet->getCellByColumnAndRow(6, $ro)->getValue();
                            $lead_notes = addslashes($lead_notes);
                            $conversion_assign_to = $objWorksheet->getCellByColumnAndRow(7, $ro)->getValue();
                            $conversion_assign_to = addslashes($conversion_assign_to);
                            $conversion_status = $objWorksheet->getCellByColumnAndRow(8, $ro)->getValue();
                            $conversion_assign_to = addslashes($conversion_assign_to);
                            $conversion_note = $objWorksheet->getCellByColumnAndRow(9, $ro)->getValue();
                            $conversion_note = addslashes($conversion_note);
                            $client_type = $objWorksheet->getCellByColumnAndRow(10, $ro)->getValue();
                            $client_type = addslashes($client_type);
                            $final_status = $objWorksheet->getCellByColumnAndRow(13, $ro)->getValue();
                            $final_status = addslashes($final_status);
                        } else {
                            $contact_id = 2;
                        }
                    } else {
                        $contact_id = 3;
                    }
                } else {
                    $contact_id = 3;
                }

                if ( strlen($contact_id) > 5 ) {
                    $lead_contact = $contact_id;
                    $lead_name = explode(' ', $lead_name);
                    $first_name = $lead_name[0];
                    $last_name = implode(' ', $lead_name);
                    $last_name = str_replace($first_name, '', $last_name);

                    $contact['lead_source'] = $lead_source;
                    $contact['customer_type'] = "lead";
                    $contact['create_date'] = date('Y-m-d H:i:s');
                    $contact['first_name'] = $first_name;
                    $contact['last_name'] = $last_name;
                    $contact['email'] = $lead_email;
                    $contact['phone1'] = $lead_contact;
                    $contact['address1'] = $lead_location;
                    $contact['note'] = $lead_notes;

                    array_push($contacts, $contact);

                    $conversion['conversion_status'] = $conversion_status;
                    $conversion['conversion_note'] = $conversion_note;
                    $conversion['client_type'] = $client_type;
                    array_push($conversions_history, $conversion);

                    $total++; // $total counts the total number of insertion into database.
                    $check = 1; // $check is used to indicate if any data are avaailable to insert.
                } else {
                    if ($contact_id == 2) {

                        $duplicate_no = $duplicate_no . $lead_contact . ' ';  // $duplicate_no is the  list of duplicate contact nunbers in the excel.
                        $total_duplicate++;
                    }
                }
            }
        }

        unlink($filename);

        if ($check == 1) {


            $j = 0;
            foreach ($contacts as $row) {
                $contact_lead = $row['lead_source'];
                $contact_type = $row['customer_type'];
                $contact_date = $row['create_date'];
                $contact_details_fname = $row['first_name'];
                $contact_details_lname = $row['last_name'];
                $contact_details_email = $row['email'];
                $contact_details_phone = $row['phone1'];
                $contact_details_address = $row['address1'];
                $contact_details_note = $row['note'];
                $contacts_value[$j] = "( '$contact_lead','$contact_type','$contact_date','$contact_details_fname','$contact_details_lname','$contact_details_email','$contact_details_phone','$contact_details_address','$contact_details_note')";
                $j++;
            }
            //echo "<pre>". print_r($contacts_value,true)."</pre>";
            $tem = $contact_sql;
            $contact_sql .= implode(',', $contacts_value);
            Sql_exec_continue($cn, $contact_sql);
            $contact_sql = $tem;
            $max_id = Sql_insert_id($cn);
//            $j = 0;
//            foreach ($contacts_details as $row) {
//
//                $contact_details_id = $max_id + $j;
//                $contact_details_fname = $row['first_name'];
//                $contact_details_lname = $row['last_name'];
//                $contact_details_email = $row['email'];
//                $contact_details_phone = $row['phone1'];
//                $contact_details_address = $row['address1'];
//                $contact_details_note = $row['note'];
//                $contacts_details_value[$j] = "( '$contact_details_id','$contact_details_fname','$contact_details_lname','$contact_details_email','$contact_details_phone','$contact_details_address','$contact_details_note')";
//                $j++;
//            }
//            $tem = $contacts_details_sql;
//            $contacts_details_sql .= implode(',', $contacts_details_value);
//            Sql_exec($cn, $contacts_details_sql);
//            $contacts_details_sql = $tem;

            $j = 0;
            foreach ($conversions_history as $row) {

                $conversion_con_id = $max_id + $j; //$row['contact_id'];
                $conversion_status = $row['conversion_status'];
                $conversion_note = $row['conversion_note'];
                $conversion_type = $row['client_type'];
                $conversions_history_value[$j] = "( '$conversion_con_id','$conversion_status','$conversion_note','$conversion_type')";
                $j++;
            }
            $tem = $conversions_history_sql;
            $conversions_history_sql .= implode(',', $conversions_history_value);
            Sql_exec_continue($cn, $conversions_history_sql);
            $conversions_history_sql = $tem;
        }
        $check = 0;
        unset($contacts);
//        unset($contacts_details);
        unset($conversions_history);
        unset($contacts_value);
//        unset($contacts_details_value);
        unset($conversions_history_value);
        $count = 0;
    }
    if (strlen($duplicate_no) > 10) {

        $return_data = array('status' => true, 'message' => '<span style="color:green;"> Total <b>' . $total . '</b> Contacts Successfully inserted.</span> and <span  style="color:red;"><b>' . $total_duplicate . '</b> Contact No is duplicated  <b>(' . $duplicate_no . ')</b></span>');
    } else {
        $return_data = array('status' => true, 'message' => '<span style="color:green;"> Total <b>' . $total . '</b> Contacts Successfully inserted.</span>');
    }
} else {

    $return_data = array('status' => false, 'message' => 'Failed : file format error!!!');
}
echo json_encode($return_data);
