<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 3/23/2017
 * Time: 5:50 PM
 */


require_once "../lib/common.php";
$action = (isset($_REQUEST['action'])) ? $_REQUEST['action'] :exit;
if( $action == "cancel" ){
    $mbid = (isset($_REQUEST['mbid'])) ? $_REQUEST['mbid'] :exit;
}else if($action == "request"){
    $mbid = (isset($_REQUEST['mbid'])) ? $_REQUEST['mbid'] :exit;
    $collected_amount = (isset($_REQUEST['collected_amount'])) ? $_REQUEST['collected_amount'] : exit;
    $collection_cost = (isset($_REQUEST['collected_cost'])) ? $_REQUEST['collected_cost'] : exit;
    $installation_cost = (isset($_REQUEST['installation_cost'])) ? $_REQUEST['installation_cost'] : exit;
    $receipt_number = (isset($_REQUEST['receipt_number'])) ? $_REQUEST['receipt_number'] : exit;

    $collected_amount = floatval($collected_amount);
    $collection_cost = floatval($collection_cost);
    $installation_cost = floatval($installation_cost);
    $total_receive = round(($collected_amount + $collection_cost+$installation_cost),4);
}else{
    echo json_encode(array("code"=>2,"msg"=>"Called with invalid Parameter"));
    exit;
}



//echo json_encode($_REQUEST);
$cn = connectDB();

if( $action == "cancel" ){
    $qry = "UPDATE monthly_bill_call_list
            SET
              `collection_status` = 'cancel'
            WHERE `id` = '$mbid';";
}elseif($action == "request"){
    $qry = "UPDATE monthly_bill_call_list
        SET
            `cash_receive` = '$collected_amount',
            `delivery_cost` = '$collection_cost',
            `installation_cost` = '$installation_cost',
            `total_receive` = '$total_receive',
            `receipt_number` = '$receipt_number',
            `collection_status`='yes',
            `receive_date` = NOW()
        WHERE `id` = '$mbid' ;";
}

$rs = Sql_exec($cn,$qry);
if($rs){
    if($action == "cancel"){
        echo json_encode(array("code"=>1,"msg"=>"Cancel Operation Successfull."));
    }elseif($action == "request"){
        echo json_encode(array("code"=>1,"msg"=>"Request Operation Successfull."));
    }
}else{
    if($action == "cancel"){
        echo json_encode(array("code"=>0,"msg"=>"Cancel Operation Failed."));
    }elseif($action == "request"){
        echo json_encode(array("code"=>0,"msg"=>"Request Operation Failed."));
    }
}
Sql_Free_Result($rs);
ClosedDBConnection($cn);