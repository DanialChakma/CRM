<?php
/**
Auto Scheduler script will be executed by cron job at the end of the day. At the night around 11 PM to 11:59 PM
Purpose of auto Scheduler is to update monthly call list table row.
 **/
$dirname = dirname(__FILE__);
$include_file = $dirname."/../lib/common.php";
include_once($include_file);
require_once "fin_lib.php";
define("MAX_ROW",200);
session_destroy();
ignore_user_abort(true);
set_time_limit(0);
date_default_timezone_set("Asia/Dhaka");
$get_tasks_url = "http://45.125.222.202/subscriptionservices_test/services/subscriber/SubscriberService_price.php";

$current_date = date("Y-m-d");
$cn = connectDB();
$status_not_cond = "PAID";
$row_num_qry = "SELECT COUNT(*) AS 'row_num' FROM monthly_bill_call_list
                WHERE email IS NOT NULL AND `status` != '$status_not_cond' AND `status` IS NOT NULL AND DATE(call_date) = '$current_date';";
$rs = Sql_exec($cn,$row_num_qry);
$dt = Sql_fetch_array($rs);
$row_count = intval($dt['row_num']);

$process_data = array();

if( $row_count >= MAX_ROW ){
    $offset = 0;
    while( $offset < $row_count ){

        $qry = "SELECT id,contact_id,email,next_renewal_date FROM monthly_bill_call_list
                WHERE email IS NOT NULL AND `status` != '$status_not_cond' AND `status` IS NOT NULL LIMIT $offset,".MAX_ROW.";";
        $rs = Sql_exec($cn,$qry);
        while( $dt = Sql_fetch_array($rs)){
            $process_data[] = array( "id" => $dt['id'], "contact" => $dt['contact_id'],"email" => $dt['email'],"renewal_date"=>$dt['next_renewal_date'] );
        }

        $offset += MAX_ROW;
    }
}else{
    $qry = "SELECT id,contact_id,email,next_renewal_date FROM monthly_bill_call_list
                WHERE email IS NOT NULL AND `status` != '$status_not_cond' AND `status` IS NOT NULL;";
    $rs = Sql_exec($cn,$qry);
    while( $dt = Sql_fetch_array($rs)){
        $process_data[] = array( "id" => $dt['id'], "contact" => $dt['contact_id'],"email" => $dt['email'],"renewal_date"=>$dt['next_renewal_date'] );
    }

}


foreach( $process_data as $key => $data_value ){
    $mb_id = $data_value['id'];
    $contat_id = $data_value['contact'];
    $email = trim($data_value['email']);
    $crm_renewal_date = trim($data_value['renewal_date']);
    //echo "Processing : ".$email." ... "."\n";

    $data = array(
        "appid"=>"test",
        "apppass"=>"test",
        "cmdid"=>"SHOW_SUBSCRIPTION_LIST",
        "cmdparam"=>"WHERE msisdn = '".trim($email)."' ORDER BY registrationDate DESC LIMIT 1"
    );

    $response = curl_request($get_tasks_url,"GET",$data);

    $data_array = explode("\n",$response);
    $res_status = trim($data_array[0]);
    $status_arr = explode("|",$res_status);
    $status = trim($status_arr[0]);
    if( $status == "+OK" ) {

        $len = count($data_array);
        $last_index = $len - 2;
        if( $last_index >= 0 ){
            $last_datas = explode("|",$data_array[$last_index]);

            $count = count($last_datas);
            $package_price_index = 9;
            $due_amount_index = 10;
            $renew_index = 7;
            $customer_status_index = 5;


            $customer_status = trim($last_datas[$customer_status_index]);

            $package_price = $last_datas[$package_price_index];
            $package_price = floatval($package_price);

            $due_amount = $last_datas[$due_amount_index];
            $due_amount = floatval($due_amount);

            $renew_date_time = $last_datas[$renew_index];
            $renew_time_stamp = strtotime($renew_date_time);
            $crm_renewal_time_stamp = strtotime($crm_renewal_date);

            if( $customer_status === "Registered" ){

                if( ($crm_renewal_time_stamp !== FALSE || $crm_renewal_time_stamp !== -1) && ( $renew_time_stamp !== FALSE || $renew_time_stamp !== -1 ) ){

                    if( $crm_renewal_time_stamp < $renew_time_stamp ){
                        $paid_str = "PAID";
                        $update_qry = "UPDATE monthly_bill_call_list SET `status`='$paid_str' WHERE  `id` = '$mb_id';";
                        $rs = Sql_exec($cn,$update_qry);
                        // if($rs) echo "Updated!!"."\n";
                        // else echo "Failed!!"."\n";

                        $call_status = "NA";
                        $call_outcome = "NA";
                        $payment_method = "NA";
                        $feedback = "Auto schedular feedback:Customer have paid all previous due amount.";
                        $insert_history = "INSERT INTO monthly_bill_call_list_history( mbcl_id,contact_id,feedback,call_status,call_outcome,payment_method)
                                  VALUES('$mb_id','$contat_id','$feedback','$call_status','$call_outcome','$payment_method');";
                        $rs = Sql_exec($cn,$insert_history);
                        if($rs) echo "History Inserted!!"."\n";
                        else echo "History Insertion Failed!!"."\n";

                    }

                    if( $crm_renewal_time_stamp ===  $renew_time_stamp ){
                       //   $check_amount = ($package_price) + ($due_amount);
                    	if( $due_amount <= 0 ){
                            $paid_str = "PAID";
                            $update_qry = "UPDATE monthly_bill_call_list SET `status`='$paid_str' WHERE  `id` = '$mb_id';";
                            $rs = Sql_exec($cn,$update_qry);
                            // if($rs) echo "Updated!!"."\n";
                            // else echo "Failed!!"."\n";

                            $call_status = "NA";
                            $call_outcome = "NA";
                            $payment_method = "NA";
                            $feedback = "Auto schedular feedback:Customer have available balance to pay due amount. During Next Renewal trail CGW will cut due amount.";
                            $insert_history = "INSERT INTO monthly_bill_call_list_history( mbcl_id,contact_id,feedback,call_status,call_outcome,payment_method)
                                  VALUES('$mb_id','$contat_id','$feedback','$call_status','$call_outcome','$payment_method');";
                            $rs = Sql_exec($cn,$insert_history);
                            if($rs) echo "History Inserted!!"."\n";
                            else echo "History Insertion Failed!!"."\n";
                        }else{
                        	$update_qry = "UPDATE monthly_bill_call_list SET `due_amount` = '$due_amount' WHERE  `id` = '$mb_id';";
                        	$rs = Sql_exec($cn,$update_qry);
                    	}
                    }

                }

            }


            if( $customer_status === "RenewalFailed" || $customer_status === "Deregistered" ){
                $update_qry = "UPDATE monthly_bill_call_list SET `due_amount`='$due_amount' WHERE  `id` = '$mb_id';";
                $rs = Sql_exec($cn,$update_qry);
                if($rs) echo "Due amount Updated!!"."\n";
                else echo "Due amount Update Failed!!"."\n";
            }
        }
    }
}


ClosedDBConnection($cn);
exit;