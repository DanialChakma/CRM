<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 9/6/2016
 * Time: 5:59 PM
 */

require_once "../lib/common.php";
require_once "fin_lib.php";
session_start();
date_default_timezone_set("Asia/Dhaka");
$user_name = $_SESSION['user_name'];
//$user_role = $_SESSION['user_role'];
$get_tasks_url = "http://103.239.252.134/subscriptionservices_test/services/subscriber/SubscriberService_price.php";
$request_data = (isset($_REQUEST['renew_date'])) ? $_REQUEST['renew_date'] : exit;
$cn = connectDB();
$renew_date = trim($request_data);

$start_date = $renew_date." ". "00:00:00";
$end_date = $renew_date ." ". "23:59:59";
$start_date = urlencode($start_date);
$end_date = urlencode($end_date);

//http://103.239.252.134/subscriptionservices_test/services/subscriber/SubscriberService.php?appid=test&apppass=test&cmdid=SHOW_SUBSCRIPTION_LIST&cmdparam=WHERE+NextRenewalDate+%3E+%272015-04-29+00%3A59%3A59%27+AND+NextRenewalDate+%3C+%272015-05-29+00%3A59%3A59%27

$data = array(
    "appid"=>"test",
    "apppass"=>"test",
    "cmdid"=>"SHOW_SUBSCRIPTION_LIST",
    "cmdparam"=>"WHERE NextRenewalDate >= '".$start_date."' AND NextRenewalDate <='".$end_date."'"
);

$req_method = "GET";
$response = curl_request($get_tasks_url,$req_method,$data);

$data_array = explode("\n",trim($response));

$status = trim($data_array[0]);
if( $status == "+OK" ){

    $len = count($data_array);
    $datas = array();
    for( $i=3; $i<($len-1); $i++ ){
        $row = trim($data_array[$i]);
        $row_arr = explode("|", $row );
        if( !empty($row_arr) ){
            $datas[] = $row_arr;
        }
    }
    unset($data_array);
    // print_r($datas);
    if( ($dlen = count($datas))>0 ){
        update_or_insert_cgw_customers_temp($cn,$datas);
    }
}

ClosedDBConnection($cn);