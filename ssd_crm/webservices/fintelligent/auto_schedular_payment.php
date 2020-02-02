<?php
/**
Auto Scheduler script will be executed by cron job at the end of the day. At the night around 11 PM to 11:59 PM
Purpose of auto Scheduler is to update monthly call list table row.
 **/
require_once "../lib/common.php";
require_once "fin_lib.php";
session_destroy();

ignore_user_abort(true);
set_time_limit(0);
date_default_timezone_set("Asia/Dhaka");
$get_tasks_url = "http://103.239.252.134/subscriptionservices_test/services/subscriber/SubscriberService_price.php";
$current_date = date("Y-m-d");
$next_date = date('Y-m-d', strtotime(' +1 day'))." "."00:00:00";
$call_status = "Connected";
$cn = connectDB();

$qry = "SELECT `mbcl_id` FROM monthly_bill_call_list_history WHERE `call_status`='$call_status' AND DATE(payment_date) = '$current_date'
        GROUP BY mbcl_id
        ORDER BY call_date DESC";


$ids = "";
//$update_mbcl_ids = array();
$rs = Sql_exec($cn,$qry);
while( $dt = Sql_fetch_array($rs) ){
    $mb_id = $dt['mbcl_id'];
    if( $ids == "" ){
        $ids = $mb_id;
    }else{
        $ids.=",".$mb_id;
    }
    //$update_mbcl_ids[] = $mb_id;
}

Sql_Free_Result($rs);

$emails = array();
if( $ids != "" ){
    $qry = "SELECT id,email FROM monthly_bill_call_list WHERE id IN (".$ids.");";
    $rs = Sql_exec($cn,$qry);
    while( $dt = Sql_fetch_array($rs) ){
        $mb_id = $dt['id'];
        $email = trim($dt['email']);
        $emails[$mb_id] = $email;
    }
}


foreach( $emails as $id => $email ){

    $status = "Deregistered";
    $data = array(
        "appid"=>"test",
        "apppass"=>"test",
        "cmdid"=>"SHOW_SUBSCRIPTION_LIST",
        "cmdparam"=>"WHERE msisdn ='".$email."' AND status <>'".$status."'"
    );

    $response = curl_request($get_tasks_url,"GET",$data);

    $data_array = explode("\n",$response);
    $res_status = trim($data_array[0]);
    if( $res_status == "+OK" ) {
        $len = count($data_array);
        $last_index = $len - 2;
        if( $last_index >= 0 ){
            $last_datas = explode("|",$data_array[$last_index]);
            $count = count($last_datas);
            $package_price_index = $count - 2;
            $due_amount_index = $count - 1;

            if( $package_price_index >= 0 ){
                $package_price = $last_datas[$package_price_index];
            }else{
                $package_price = 0;
            }

            if( $due_amount_index >= 0 ){
                $due_amount = $last_datas[$due_amount_index];
            }else{
                $due_amount = 0;
            }

            $due_amount = floatval($due_amount);
            if( $due_amount > 0 ){
                    $update_qry = "UPDATE monthly_bill_call_list SET `call_date`='$next_date' WHERE  `id` = '$id';";
                    $rs = Sql_exec($cn,$update_qry);
                    if($rs) echo "Updated!!"."<br/>";
                    else echo "Failed!!"."<br/>";
            }else{

            }
        }
    }
}


ClosedDBConnection($cn);
exit;