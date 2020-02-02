<?php
/**
Auto Scheduler script will be executed by cron job at the end of the day. At the night around 11 PM to 11:59 PM
Purpose of auto Scheduler is to update monthly call list table row.
 **/
require_once "../lib/common.php";
require_once "fin_lib.php";
define("MAX_ROW",200);
session_destroy();
ignore_user_abort(true);
set_time_limit(0);
date_default_timezone_set("Asia/Dhaka");
$get_tasks_url = "http://103.218.27.138/radiusservices/user_details.php";
//radius_user_name
//radius_user_id
$current_date = date("Y-m-d");
$cn = connectDB();

$row_num_qry = "SELECT COUNT(*) AS 'row_num' FROM monthly_bill_call_list
                WHERE radius_user_name IS NOT NULL AND radius_user_id IS NOT NULL AND `status` <> 'PAID'  AND `status` IS NOT NULL AND DATE(call_date) = '$current_date';";
$rs = Sql_exec($cn,$row_num_qry);
$dt = Sql_fetch_array($rs);
$row_count = intval($dt['row_num']);

$process_data = array();

if( $row_count >= MAX_ROW ){
    $offset = 0;
    while( $offset < $row_count ){

        $qry = "SELECT id,radius_user_name,radius_user_id,email,next_renewal_date FROM monthly_bill_call_list
                WHERE radius_user_name IS NOT NULL AND radius_user_id IS NOT NULL  AND `status` <> 'PAID' AND `status` IS NOT NULL LIMIT $offset,".MAX_ROW.";";
        $rs = Sql_exec($cn,$qry);
        while( $dt = Sql_fetch_array($rs)){
            $process_data[] = array( "id" => $dt['id'], "user" => $dt['radius_user_name'],"user_id" => $dt['radius_user_id'],"renewal_date"=>$dt['next_renewal_date'] );
        }

        $offset += MAX_ROW;
    }
}else{
    $qry = "SELECT id,radius_user_name,radius_user_id,email,next_renewal_date FROM monthly_bill_call_list
            WHERE radius_user_name IS NOT NULL AND radius_user_id IS NOT NULL  AND `status` <> 'PAID' AND `status` IS NOT NULL;";
    $rs = Sql_exec($cn,$qry);
    while( $dt = Sql_fetch_array($rs)){
        $process_data[] = array( "id" => $dt['id'], "user" => $dt['radius_user_name'],"user_id" => $dt['radius_user_id'],"renewal_date"=>$dt['next_renewal_date'] );
    }
}


foreach( $process_data as $key => $data_value ){
    $mb_id = $data_value['id'];
    $radius_renewal_date = date("Y-m-d",strtotime(trim($data_value['renewal_date'])));
    $radius_user_name = trim($data_value['user']);
    $radius_user_id = trim($data_value['user_id']);
    //echo "Processing : ".$email." ... "."\n";

    $data = array(
        "user" => $radius_user_name,
        "dozeid" => $radius_user_id,
        "manager"=>"crmwebservice",
        "managerpass"=>"crm_doze"
    );

    $response = curl_request($get_tasks_url,"GET",$data);
    $response = trim($response);
    $attributes = explode("|",$response);

    if( count($attributes) > 0 ){
        $response_status =  trim($attributes[0]);
        if( $response_status == "SUCCESS" ){
            if( count($attributes) == 15 ){
                $rad_user_name = trim($attributes[1]);
                $rad_user_id = trim($attributes[2]);
                $rad_current_expiration_date = date("Y-m-d",strtotime(trim($attributes[12])));
                $current_balance = round(floatval(trim($attributes[8])),4);
                $package_unit_price = round(floatval(trim($attributes[13])),4);
                $package_unit_price_tax = round(floatval(trim($attributes[14])),4);
                $due_amount = round(( ($package_unit_price+$package_unit_price_tax) - $current_balance ),4);
                if( $radius_renewal_date == $rad_current_expiration_date ){

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
                        if($rs) echo "History Inserted!!"."\n";
                        else echo "History Insertion Failed!!"."\n";
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

}


ClosedDBConnection($cn);
exit;