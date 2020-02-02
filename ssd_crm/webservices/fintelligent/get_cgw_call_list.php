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
$request_data = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;
$cn = connectDB();
$renew_date = trim($request_data['renew_date']);
$previous_6th_day = date( "Y-m-d", strtotime("-4 day",strtotime($renew_date)) );
$previous_8th_day = date( "Y-m-d", strtotime("-8 day",strtotime($renew_date)) );
$condition_in_data = "'".$previous_6th_day."','".$previous_8th_day."'";
$current_date = date("Y-m-d");
$start_date = $renew_date." ". "00:00:00";
$end_date = $renew_date ." ". "23:59:59";

$qry_exist = "SELECT COUNT(1) as 'num' FROM `monthly_bill_call_list` WHERE DATE_FORMAT(next_renewal_date,'%Y-%m-%d') = '$renew_date';";
$rs = Sql_exec($cn,$qry_exist);
$dt = Sql_fetch_array($rs);
$count = intval($dt['num']);
if( $count > 0 ){
    echo json_encode(array(
        "status"=> 0,
        "msg"=>"Call List Already Generated for $renew_date Date"
    ));
    ClosedDBConnection($cn);
    exit;
}
//http://103.239.252.134/subscriptionservices_test/services/subscriber/SubscriberService.php?appid=test&apppass=test&cmdid=SHOW_SUBSCRIPTION_LIST&cmdparam=WHERE+NextRenewalDate+%3E+%272015-04-29+00%3A59%3A59%27+AND+NextRenewalDate+%3C+%272015-05-29+00%3A59%3A59%27

$data = array(
    "appid"=>"test",
    "apppass"=>"test",
    "cmdid"=>"SHOW_SUBSCRIPTION_LIST",
    "cmdparam"=>"WHERE NextRenewalDate>='".$start_date."' AND NextRenewalDate<='".$end_date."'"
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

        $current_emails = array();
        $in_clause = "";
        for($i=0; $i<$dlen; $i++ ){
            $current_emails[] = trim($datas[$i][0]);
        }

        $in_clause = implode("\",\"",$current_emails);
        $in_clause = '"'.$in_clause.'"';

        $qry = "SELECT id,email FROM contacts WHERE email IN (".$in_clause.");";
        $emails_contact = array();
        $rs = Sql_exec($cn,$qry);
        while( $dt= Sql_fetch_array($rs) ){
            $email = trim($dt['email']);
            $emails_contact[$email] = trim($dt['id']);
        }

        $value_string = "";
        for( $i=0; $i<$dlen; $i++ ){
            $due_amount = $datas[$i][10];// 10 index is for due amount
            $email = trim($datas[$i][0]);// 0 index is for email
            $status = trim($datas[$i][5]);// 5 index is for customer cgw status
            // generate call list only for those customer which has due amount is greater than zero
            if( is_numeric($due_amount) ){
                $due_amount = floatval($due_amount);
            }else{
                $due_amount = 0;
            }


            if( $due_amount > 0){
                if( $status == "RenewalFailed" || $status == "Registered" ){
                    $customer_id = trim($emails_contact[$email]);
                    $customer_id = ($customer_id == "" || $customer_id == null) ? "": $customer_id;
                    if( $customer_id != ""){
                        if( $value_string != "" ){
                            $value_string .=","."(".$customer_id.",'".$email."','".$current_date."','".$datas[$i][6]."','".$datas[$i][7]."','".$datas[$i][9]."','".$datas[$i][10]."','".$user_name."','"."INITIATED"."')";
                        } else {
                            $value_string = "(".$customer_id.",'".$email."','".$current_date."','".$datas[$i][6]."','".$datas[$i][7]."','".$datas[$i][9]."','".$datas[$i][10]."','".$user_name."','"."INITIATED"."')";
                        }
                    }else{
                        $customer_id = -1;
                        if( $value_string != "" ){
                            $value_string .=","."(".$customer_id.",'".$email."','".$current_date."','".$datas[$i][6]."','".$datas[$i][7]."','".$datas[$i][9]."','".$datas[$i][10]."','".$user_name."','"."INITIATED"."')";
                        } else {
                            $value_string = "(".$customer_id.",'".$email."','".$current_date."','".$datas[$i][6]."','".$datas[$i][7]."','".$datas[$i][9]."','".$datas[$i][10]."','".$user_name."','"."INITIATED"."')";
                        }
                    }
                }
            }
        }

       $insert_qry = "INSERT INTO monthly_bill_call_list ( contact_id,email,call_date,charging_due_date,next_renewal_date,package_price,due_amount,generated_by,`status`)
                       VALUES ".$value_string.";";
        if( $value_string != "" ){
            try{
                $rs = Sql_exec($cn,$insert_qry);
                update_or_insert_cgw_customers($cn,$datas);
            }catch (Exception $e){
            }
        }

        unset($datas);

        $qry = "SELECT id,contact_id FROM monthly_bill_call_list WHERE DATE(next_renewal_date) IN (".$condition_in_data.") AND `status` != 'PAID';";
        $rs = Sql_exec($cn,$qry);
        while( $dt = Sql_fetch_array($rs) ){
            $mbid = trim($dt['id']);
            $contact_id = trim($dt['contact_id']);
            $update_qry = "UPDATE monthly_bill_call_list SET `call_date`= '$current_date' WHERE `id` = '$mbid' AND `contact_id` = '$contact_id';";
            Sql_exec($cn,$update_qry);
            //print_r($dt);
        }

        echo json_encode(array(
            "status" => 1,
            "msg" =>"Call List Generated Successfully."
        ));
    }else{
        echo json_encode(array(
            "status" => 2,
            "msg" =>"There is no data for Renew date:$renew_date"
        ));
    }
}else{
    echo json_encode(array(
        "status"=> 0,
        "msg"=>"Failed to generate call list for $renew_date Date"
    ));
}



ClosedDBConnection($cn);