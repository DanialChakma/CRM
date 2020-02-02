<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 10/4/2016
 * Time: 3:03 PM
 */

require_once "../lib/common.php";
require_once "fin_lib.php";
$param = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;
date_default_timezone_set("Asia/Dhaka");
$get_tasks_url = "http://103.239.252.134/subscriptionservices_test/services/subscriber/SubscriberService_price.php";
$cn = connectDB();
$mb_id = mysql_real_escape_string(trim($param['mbcl_id']));

$mb_qry = "SELECT `contact_id`,`email`,`status`,next_renewal_date FROM `monthly_bill_call_list` WHERE id = '$mb_id';";
$rs = Sql_exec($cn,$mb_qry);
$dt = Sql_fetch_array($rs);
$crm_renewal_date = trim($dt['next_renewal_date']);
$contat_id = $dt['contact_id'];
$email = trim($dt['email']);
$status_check = trim($dt['status']);
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

                //echo "CRM_RENWAL:".$crm_renewal_time_stamp.", Current Renewal:".$renew_time_stamp."\n";
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
                    //if($rs) echo "History Inserted!!"."\n";
                    //else echo "History Insertion Failed!!"."\n";

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
                       // if($rs) echo "History Inserted!!"."\n";
                       // else echo "History Insertion Failed!!"."\n";
                    }else{
                        $update_qry = "UPDATE monthly_bill_call_list SET `due_amount` = '$due_amount' WHERE  `id` = '$mb_id';";
                        $rs = Sql_exec($cn,$update_qry);
                    }
                }

            }

        }


        if( $customer_status === "RenewalFailed" || $customer_status === "Deregistered" ){
            $update_qry = "UPDATE monthly_bill_call_list SET `due_amount` = '$due_amount' WHERE  `id` = '$mb_id';";
            $rs = Sql_exec($cn,$update_qry);
            //if($rs) echo "Due amount Updated!!"."\n";
            //else echo "Due amount Update Failed!!"."\n";
        }
    }
}

unset($data_array);
unset($response);

$data = array();

$qry = "SELECT email, feedback, call_date, next_follow_up_date, call_status, call_outcome
        FROM monthly_bill_call_list_history WHERE mbcl_id='$mb_id' ORDER BY call_date DESC;";
$rs = Sql_exec($cn,$qry);
$i = 0;
while( $dt = Sql_fetch_array($rs) ){
    $j=0;

    $call_outcome = trim($dt['call_outcome']);
    if( $call_outcome == 1 || $call_outcome == "1"){
        $call_outcome = "Followup";
    }elseif( $call_outcome == 2 || $call_outcome == "2" ){
        $call_outcome = "Not Interested to pay";
    }elseif( $call_outcome == 3 || $call_outcome == "3" ){
        $call_outcome = "Pay Today";
    }elseif($call_outcome == 4 || $call_outcome == "4" ){
        $call_outcome = "Pay Later";
    }

    $customer_id = trim($dt['email']);
    $data[$i][$j++] = $customer_id;
    $data[$i][$j++]= trim($dt['feedback']);
    $data[$i][$j++]= trim($dt['call_date']);
    $data[$i][$j++]= trim($dt['next_follow_up_date']);
    $data[$i][$j++]= trim($dt['call_status']);
    $data[$i][$j++]= $call_outcome;
    $i++;
}

echo json_encode($data);

ClosedDBConnection($cn);