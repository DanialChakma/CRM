<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 8/18/2016
 * Time: 7:51 PM
 */

require_once "../lib/common.php";
require_once "fin_lib.php";
$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;

$assign_to = trim($datainfo['assign_id']);
$reassign_date = trim($datainfo['reassign_date']);
$bill_ids = $datainfo['ids'];

$cn = connectDB();

$status_arr = array();

foreach($bill_ids  as $id){
    $qry = "SELECT `collection_status` FROM monthly_bill_call_list WHERE id='$id';";
    $rs = Sql_exec($cn,$qry);
    $dt = Sql_fetch_array($rs);
    $status_arr[$id] = trim($dt['collection_status']);
}

$res_array = array();

foreach($bill_ids  as $id){

    if( $status_arr[$id] != "confirm" ){
        if( !empty($reassign_date) ){
            $qry = "UPDATE monthly_bill_call_list
            SET `collection_agent` = '$assign_to',
                `collection_date` = '$reassign_date',
                `collection_status`='no'
            WHERE id = '$id';";
        }else{
            $qry = "UPDATE monthly_bill_call_list
            SET `collection_agent` = '$assign_to',
                `collection_date` = DATE(NOW()),
                `collection_status`='no'
            WHERE id = '$id';";
        }
        $rs = Sql_exec($cn,$qry);
        if( $rs ){
            $res_array[] = array( "bill_id"=> $id,"msg"=>"Successfully Assigned.");
        }else{
            $res_array[] = array( "bill_id"=> $id,"msg"=>"Failed to Assign.");
        }
    }
}

ClosedDBConnection($cn);
echo json_encode($res_array);