<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 9/25/2016
 * Time: 4:43 PM
 */
set_time_limit(0);
define("CHUNK_NUMBER",1000);
require_once "../lib/common.php";
date_default_timezone_set("Asia/Dhaka");
$data = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;
//print_r($data);
//exit;
$cn = connectDB();

$user_name = trim($_SESSION['user_name']);
$is_multiple = $data['is_multiple'];
$book_id = mysql_real_escape_string(trim($data['book_id']));
$branch = trim($data['branch']);
$check_prefix = mysql_real_escape_string(trim($data['check_prefix']));
$check_initial_number = intval(trim($data['check_initial_number']));
$check_number_diff = intval(trim($data['check_number_diff']));
$checkbox_number = intval(trim($data['checkbox_number']));
$remarks = mysql_real_escape_string(trim($data['remarks']));


$status = "NEW";
if( $is_multiple && $checkbox_number > 0 ){
    $initial_invoice_id = $check_prefix.$check_initial_number;

    $current_check_number = $check_initial_number;

    $chunk_number = intval($checkbox_number/CHUNK_NUMBER);
    $remainder = $checkbox_number % CHUNK_NUMBER;
    for($i=0;$i<$chunk_number;$i++){
        $qry = "INSERT INTO chequebooks ( InvoiceBookID,InvoiceInitialID,InvoiceNo,Branch,`Status`,Remarks,UserID, LastUpdate ) VALUES ";
        $value_str = "";
        for( $j=1; $j<=CHUNK_NUMBER; $j++ ){
            $invoice_no =  $check_prefix.$current_check_number;
            $current_time_stamp = date('Y-m-d H:i:s');
            if( $value_str == "" ){
                $value_str = "('".$book_id."','".$initial_invoice_id."','".$invoice_no."','".$branch."','".$status."','".$remarks."','".$user_name."','".$current_time_stamp."')";
            }else{
                $value_str .=","."('".$book_id."','".$initial_invoice_id."','".$invoice_no."','".$branch."','".$status."','".$remarks."','".$user_name."','".$current_time_stamp."')";
            }

            $current_check_number+=$check_number_diff;
        }

        $qry.= $value_str.";";
        $rs = mysql_query("START TRANSACTION",$cn);
        if($rs){
            $rs_insert = mysql_query($qry,$cn);
            if($rs_insert){
                mysql_query("COMMIT",$cn);
            }else{
                mysql_query("ROLLBACK",$cn);
            }
            Sql_Free_Result($rs_insert);
        }

        Sql_Free_Result($rs);
        unset($value_str);
        unset($qry);

    }



    $qry = "INSERT INTO chequebooks ( InvoiceBookID,InvoiceInitialID,InvoiceNo,Branch,`Status`,Remarks,UserID, LastUpdate ) VALUES ";
    $value_str = "";
    for( $j=1; $j<=$remainder; $j++ ){
        $invoice_no =  $check_prefix.$current_check_number;
        $current_time_stamp = date('Y-m-d H:i:s');
        if( $value_str == "" ){
            $value_str = "('".$book_id."','".$initial_invoice_id."','".$invoice_no."','".
                $branch."','".$status."','".$remarks."','".
                $user_name."','".$current_time_stamp."')";
        }else{
            $value_str .=","."('".$book_id."','".$initial_invoice_id."','".$invoice_no."','".
                $branch."','".$status."','".$remarks."','".
                $user_name."','".$current_time_stamp."')";
        }

        $current_check_number+=$check_number_diff;
    }

    $qry.= $value_str.";";
    $rs = mysql_query("START TRANSACTION",$cn);
    if($rs){
        $rs_insert = mysql_query($qry,$cn);
        if($rs_insert){
            mysql_query("COMMIT",$cn);
        }else{
            mysql_query("ROLLBACK",$cn);
        }
        Sql_Free_Result($rs_insert);
    }

    Sql_Free_Result($rs);
    unset($value_str);
    unset($qry);

}else{
    $current_time_stamp = date('Y-m-d H:i:s');
    $initial_invoice_id = $check_prefix.$check_initial_number;
    $invoice_no =  $check_prefix.$check_initial_number;
    $qry = "INSERT INTO chequebooks ( InvoiceBookID,InvoiceInitialID,InvoiceNo,Branch,`Status`,Remarks,UserID, LastUpdate )
                VALUES ( '$book_id', '$initial_invoice_id', '$invoice_no', '$branch', '$status', '$remarks', '$user_name', '$current_time_stamp' );";

    Sql_exec($cn,$qry);
}

echo json_encode(array("status"=>0,"msg"=>"Invoice Generation Successful."));
ClosedDBConnection($cn);