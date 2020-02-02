<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 5/31/2017
 * Time: 6:12 PM
 */
require_once "../lib/common.php";
require_once "fin_lib.php";
session_start();
date_default_timezone_set("Asia/Dhaka");
$user_name = $_SESSION['user_name'];
//http://103.218.27.138/radiusservices/user_details.php?user=akram&dozeid=100021&manager=crmwebservice&managerpass=crm_doze&expiration=2017-04-17%2000:00:00
$get_tasks_url = "http://103.218.27.138/radiusservices/user_details.php";
$request_data = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;
$renew_date = trim($request_data['renew_date']);
$previous_6th_day = date( "Y-m-d", strtotime("-4 day",strtotime($renew_date)) );
$previous_8th_day = date( "Y-m-d", strtotime("-8 day",strtotime($renew_date)) );
$condition_in_data = "'".$previous_6th_day."','".$previous_8th_day."'";
$current_date = date("Y-m-d");
$cn = connectDB();
/*
$qry_exist = "SELECT COUNT(1) as 'num' FROM `monthly_bill_call_list` WHERE  DATE_FORMAT(next_renewal_date,'%Y-%m-%d') = '$renew_date';";
$rs = Sql_exec($cn,$qry_exist);
$dt = Sql_fetch_array($rs);
$count = intval($dt['num']);
if( $count > 0 ){
    echo json_encode(array(
        "status"=> 0,
        "msg"=>"Call List Already Generated for $renew_date Date"
    ));
    Sql_Free_Result($rs);
    ClosedDBConnection($cn);
    exit;
} */

$already_generated_customers = array();
$qry = "SELECT radius_user_name, radius_user_id FROM monthly_bill_call_list WHERE `status` <> 'PAID' AND DATE_FORMAT(next_renewal_date,'%Y-%m-%d') = '$renew_date';";
$rs = Sql_exec($cn,$qry);
while( $dt = Sql_fetch_array($rs) ){
    $already_generated_customers[] = trim($dt['radius_user_name']);
}

$data = array(
    "manager"=>"crmwebservice",
    "managerpass"=>"crm_doze",
    "expiration"=>$renew_date,
);

$req_method = "GET";
$response = curl_request($get_tasks_url,$req_method,$data);

$lines = explode("\r\n",$response);
foreach($lines as $key=>$value){

    $val_str = trim($value);
    $attributes = explode("|",$val_str);
    if( trim($attributes[0]) == "SUCCESS" ){

    }else if( trim($attributes[0]) == "FAILED" ){
         unset($lines[$key]);
    }else{
        if( $key > 0 ){
            $lines[$key-1] = $lines[$key-1]."\n".$lines[$key];
            unset($lines[$key]);
        }
    }
}
$datas = array();
$monthly_call_list_str = "";
foreach($lines as $k=>$value){
    $values = explode("|",$value);

    if( !empty($values) && count($values) == 15 ){
        $datas[] = $values;
        $radius_user_name = trim($values[1]);
        $current_balance = round(floatval(trim($values[8])),4);
        $package_unit_price = round(floatval(trim($values[13])),4);
        $package_unit_price_tax = round(floatval(trim($values[14])),4);
        $due_amount = round(( ($package_unit_price+$package_unit_price_tax) - $current_balance ),4);
        if( array_search($radius_user_name,$already_generated_customers) === FALSE  ){
            if( $due_amount > 0 ){
                if( $monthly_call_list_str != "" ){
                    //email,call_date,charging_due_date,next_renewal_date,package_price,due_amount,generated_by,`status`
                    // user_name,user_id/doze_id,email,call_date,charging_due_date,next_renewal_date,package_price,package_price_tax,current_credits,due_amount,generated_by,`status`
                    $monthly_call_list_str .=","."('".$values[1]."','".$values[2]."','".$values[9]."','".$current_date."','".$values[11]."','".$values[12]."','".$values[13]."','".$values[14]."','".$values[8]."','".$due_amount."','"."MDB"."','".$user_name."','"."INITIATED"."')";
                } else {
                    $monthly_call_list_str = "('".$values[1]."','".$values[2]."','".$values[9]."','".$current_date."','".$values[11]."','".$values[12]."','".$values[13]."','".$values[14]."','".$values[8]."','".$due_amount."','"."MDB"."','".$user_name."','"."INITIATED"."')";
                }
            }
        }
    }
}


if( count($lines) > 0 ){

    if( $monthly_call_list_str != "" ){

        $qry = "INSERT INTO monthly_bill_call_list(radius_user_name,radius_user_id,email,call_date,charging_due_date,next_renewal_date,package_price,package_price_tax,current_credits,due_amount,bill_type,generated_by,`status` ) VALUES ".$monthly_call_list_str.";";
        try{
            $rs = Sql_exec($cn,$qry);
            update_or_insert_cgw_customers($cn,$datas);

            unset($datas);
            $qry = "SELECT id FROM monthly_bill_call_list WHERE DATE(next_renewal_date) IN (".$condition_in_data.") AND `status` != 'PAID';";
            $rs = Sql_exec($cn,$qry);
            while( $dt = Sql_fetch_array($rs) ){
                $mbid = trim($dt['id']);
                $update_qry = "UPDATE monthly_bill_call_list SET `call_date`= '$current_date' WHERE `id` = '$mbid';";
                Sql_exec($cn,$update_qry);
            }

            echo json_encode(array(
                "status" => 1,
                "msg" =>"Call List Generated Successfully."
            ));

        }catch (Exception $e){
            echo json_encode(array(
                "status" => 2,
                "msg" =>"Error Occured. Message:".$e.getMessage()
            ));
        }
    }else{
        echo json_encode(array(
            "status" => 1,
            "msg" =>"No call list to generate."
        ));
    }

}else{
    echo json_encode(array(
        "status" => 3,
        "msg" =>"There is no data for Renew date:$renew_date"
    ));
}


ClosedDBConnection($cn);







