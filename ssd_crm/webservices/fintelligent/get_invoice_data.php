<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 9/29/2016
 * Time: 6:56 PM
 */

require_once "../lib/common.php";
require_once "fin_lib.php";
date_default_timezone_set("Asia/Dhaka");
$params = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;

$cn = connectDB();

$bill_type = trim($params['bill_type']);
$bill_id = trim($params['bill_id']);


if( $bill_type == "MDB" ){
    $qry = "SELECT cash_receive,invoice_book_id,receipt_number
            FROM monthly_bill_call_list WHERE transaction_id='$bill_id'";
    $rs = Sql_exec($cn,$qry);
    $dt = Sql_fetch_array($rs);
    echo json_encode(array(
        "amount" => trim($dt['cash_receive']),
        "bookid" => trim($dt['invoice_book_id']),
        "invoiceno" => trim($dt['receipt_number'])
    ));

}else{
    $qry = "SELECT pay.cash_receive,pay.receipt_bookid,pay.receipt_number FROM payments AS pay INNER JOIN customer_conversion AS cust_conv ON pay.contact_id = cust_conv.contact_id
            WHERE cust_conv.transaction_id = '$bill_id';";
    $rs = Sql_exec($cn,$qry);
    $dt = Sql_fetch_array($rs);
    echo json_encode(array(
        "amount" => trim($dt['cash_receive']),
        "bookid" => trim($dt['receipt_bookid']),
        "invoiceno" => trim($dt['receipt_number'])
    ));
}

ClosedDBConnection($cn);

