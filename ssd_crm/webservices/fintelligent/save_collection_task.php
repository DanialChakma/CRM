<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 6/8/2017
 * Time: 3:27 AM
 */

require_once "../lib/common.php";
require_once "fin_lib.php";
session_start();
date_default_timezone_set("Asia/Dhaka");
$login_user_name = $_SESSION['user_name'];
$mbcl_id = $_REQUEST['mbcl_id'];
$payment_date = trim($_REQUEST['payment_date']);
if( empty($payment_date) ){
    $payment_date = date("Y-m-d");
}
$transaction_id = substr(uniqid("DB",true),0,16);
$cn = connectDB();
if( isset($mbcl_id) && !empty($mbcl_id) ){

    $feedback = "Generated By Customer Phone Call.";
    $qry = "UPDATE monthly_bill_call_list
            SET   `transaction_id` = '$transaction_id',
                  `update_count` = IFNULL(update_count, 0)+1,
                  `payment_date` = '$payment_date',
                  `status` = '3',
                  `remarks` = '$feedback',
				  `payment_method`='3'
            WHERE  `id` = '$mbcl_id';";

    try{
        $rs = Sql_exec($cn,$qry);
        if($rs){
            echo json_encode(array("status"=>"success","msg"=>"Collection Task Successfully Generated."));
        }else{
            echo json_encode(array("status"=>"error","msg"=>"Collection Task Generation Failed."));
        }
    }catch (Exception $ex){
        $error_str = $ex.getMessage();
        echo json_encode(array("status"=>"error","msg"=>$error_str));
    }
}else{

    $user_name = trim($_REQUEST['user_name']);
    $doze_id = trim($_REQUEST['user_id']);
    $customer_full_name = trim($_REQUEST['customer_full_name']);
    $user_mobile = trim($_REQUEST['phone_no']);
    $address = trim($_REQUEST['address']);
    $city = trim($_REQUEST['city']);
    $email = trim($_REQUEST['email']);

    $package = trim($_REQUEST['package']);
    $creation_date = trim($_REQUEST['creation_date']);
    $creation_date = date('Y-m-d',strtotime($creation_date));
    $expiration_date = trim($_REQUEST['expiration_date']);
    $expiration_date = date('Y-m-d',strtotime($expiration_date));

    $current_balance = round(floatval(trim($_REQUEST['current_balance'])),4);
    $package_unit_price = round(floatval(trim($_REQUEST['package_unit_price'])),4);
    $package_unit_price_tax = round(floatval(trim($_REQUEST['package_unit_price_tax'])),4);
    $package_price = $package_unit_price + $package_unit_price_tax;
    $due_amount = round(( ($package_unit_price+$package_unit_price_tax) - $current_balance ),4);
    $feedback = "Generated By Customer Phone Call.";
    $qry = "INSERT INTO monthly_bill_call_list ( radius_user_name,radius_user_id,email,call_date,charging_due_date,next_renewal_date,package_price,package_price_tax,current_credits,due_amount,bill_type,generated_by,`status`,`payment_method`,`payment_date`,`update_count`,`remarks` )
            VALUES (
                    '$user_name',
                    '$doze_id',
                    '$email',
                     DATE(NOW()),
                    '$creation_date',
                    '$expiration_date',
                    '$package_unit_price',
                    '$package_unit_price_tax',
                    '$current_balance',
                    '$due_amount',
                    'MDB',
                    '$login_user_name',
                    '3',
                    '3',
                    '$payment_date',
                    1,
                    '$feedback'
            );";
    try{
        $rs = Sql_exec($cn,$qry);
        if($rs){
            $qry_basic_info =  "INSERT INTO cgw_customers( radius_user_name, radius_user_id, customer_name, email, phone_no_p, present_address_1, permanent_address, city, package )
                               VALUES ('$user_name','$doze_id','$customer_full_name','$email','$user_mobile','$address','$address','$city','$package');";
            Sql_exec($cn,$qry_basic_info);
            echo json_encode(array("status"=>"success","msg"=>"Collection Task Successfully Generated."));
        }else{
            echo json_encode(array("status"=>"error","msg"=>"Collection Task Generation Failed."));
        }
    }catch (Exception $ex){
        $error_str = $ex.getMessage();
        echo json_encode(array("status"=>"error","msg"=>$error_str));
    }
}


ClosedDBConnection($cn);
//$request_data = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;
