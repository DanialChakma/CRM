<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 6/1/2017
 * Time: 5:39 PM
 */

require_once "../lib/common.php";
require_once "fin_lib.php";
date_default_timezone_set("Asia/Dhaka");
$param = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;

if( empty($param['mbcl_id']) ){
    echo json_encode(array("status"=>0,"msg"=>"Monthly Bill Call ID Required."));
    exit;
}


$get_tasks_url = "http://103.218.27.138/radiusservices/user_details.php";

$cn = connectDB();
$mb_id = mysql_real_escape_string(trim($param['mbcl_id']));

$mb_qry = "SELECT `radius_user_name`,`radius_user_id`,`status`,next_renewal_date FROM `monthly_bill_call_list` WHERE id = '$mb_id';";
$rs = Sql_exec($cn,$mb_qry);
$dt = Sql_fetch_array($rs);
$crm_renewal_date = date("Y-m-d",strtotime(trim($dt['next_renewal_date'])));
$radius_user_name = trim($dt['radius_user_name']);
$radius_user_id = trim($dt['radius_user_id']);
$status_check = trim($dt['status']);


$data = array(
    "user" => $radius_user_name,
    "dozeid" => $radius_user_id,
    "manager"=>"crmwebservice",
    "managerpass"=>"crm_doze"
);

$response = curl_request($get_tasks_url,"GET",$data);
$response = trim($response);
$attributes = explode("|",$response);
/*
echo "<pre>";
print_r($attributes);
echo "</pre>";
*/
if( count($attributes) > 0 ){

    $response_status =  trim($attributes[0]);
    if( $response_status == "SUCCESS" ){
        if( count($attributes) == 15 ){
            $rad_expiration_date = date("Y-m-d", strtotime(trim($attributes[12])));
            $rad_user_name = trim($attributes[1]);
            $rad_user_id = trim($attributes[2]);
            $current_balance = round(floatval(trim($attributes[8])),4);
            $package_unit_price = round(floatval(trim($attributes[13])),4);
            $package_unit_price_tax = round(floatval(trim($attributes[14])),4);
            $due_amount = round(( ($package_unit_price+$package_unit_price_tax) - $current_balance ),4);

            if( $crm_renewal_date == $rad_expiration_date ){
                if( $due_amount <= 0 ){
                    $paid_str = "PAID";
                    $update_qry = "UPDATE monthly_bill_call_list SET `status`='$paid_str' WHERE  `id` = '$mb_id' AND `radius_user_name`='$radius_user_name' AND `radius_user_id`='$radius_user_id';";
                    $rs = Sql_exec($cn,$update_qry);

                    $call_status = "NA";
                    $call_outcome = "NA";
                    $payment_method = "NA";
                    $feedback = "Auto schedular feedback:Customer now have available balance to pay due amount. During Next Renewal trail Radius will cut due amount.";
                    $insert_history = "INSERT INTO monthly_bill_call_list_history( mbcl_id,feedback,call_status,call_outcome,payment_method)
                                  VALUES('$mb_id','$feedback','$call_status','$call_outcome','$payment_method');";
                    $rs = Sql_exec($cn,$insert_history);
                }else{
                    $update_qry = "UPDATE monthly_bill_call_list
                               SET  `current_credits`='$current_balance',
                                    `package_price`='$package_unit_price',
                                    `package_price_tax`='$package_unit_price_tax',
                                    `due_amount` = '$due_amount'
                               WHERE  `id` = '$mb_id' AND `radius_user_name`='$radius_user_name' AND `radius_user_id`='$radius_user_id';";
                    $rs = Sql_exec($cn,$update_qry);
                    //echo "Due amount > 0";
                }
            }

        }
    }
}


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
