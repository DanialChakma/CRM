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
$user_name = $_SESSION['user_name'];
//$user_role = $_SESSION['user_role'];
$get_tasks_url = "http://103.239.252.134/subscriptionservices_test/services/subscriber/SubscriberService_price.php";
$request_data = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;
$cn = connectDB();
$renew_date = trim($request_data['renew_date']);

$start_date = $renew_date." ". "00:00:00";
$end_date = $renew_date ." ". "23:59:59";

$qry_exist = "SELECT COUNT(1) as 'num' FROM monthly_bill_call_list WHERE DATE_FORMAT(next_renewal_date,'%Y-%m-%d') = '$renew_date';";
$rs = Sql_exec($cn,$qry_exist);
$dt = Sql_fetch_array($rs);
$count = intval($dt['num']);
if( $count > 0 ){
    echo json_encode(array(
        "status"=> 0,
        "msg"=>"Call Already Generated for $renew_date Date"
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

$param = http_build_query($data);
$url_to_hit = $get_tasks_url."?".$param;

$req_method = "GET";
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
curl_setopt($ch, CURLOPT_URL, $url_to_hit);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"GET");
$response = "";
if( ($response = curl_exec($ch)) === FALSE ){
    // echo "ERROR:". curl_error($ch);
}else{
    //  echo "Server Res:".$response;
}

curl_close($ch);

//msisdn|parentID|SubscriptionGroupID|registrationDate|ServiceDuration|status|ChargingDueDate|NextRenewalDate|ServiceID
//jafryshamim@hotmail.com|Internet|Internet10Mbps45GB|2015-04-29 19:40:59|30|Registered|2016-09-07 20:42:52|2016-09-07 20:42:52|ISP

$data_array = explode("\n",$response);
$status = trim($data_array[0]);
if( $status == "+OK" ){

    $len = count($data_array);
    for($i=3;$i<($len-1);$i++){$row = $data_array[$i];$datas[] = explode("|",$row);}
    unset($data_array);

    if( ($dlen = count($datas))>0 ){
        $in_clause = "";
        for($i=0;$i<$dlen;$i++){
            if( $in_clause != "" ){
                $in_clause .= ","."'".trim($datas[$i][0])."'";
            } else{
                $in_clause = "'".trim($datas[$i][0])."'";
            }
        }

        $qry = "SELECT id,email FROM contacts WHERE email IN (".$in_clause.");";
        $emails_contact = array();
        $rs = Sql_exec($cn,$qry);
        while($dt= Sql_fetch_array($rs)){
            $email = trim($dt['email']);
            $emails_contact[$email] = trim($dt['id']);
        }
        $value_string = "";
        for($i=0;$i<$dlen;$i++){
            $email = trim($datas[$i][0]);
            $status = trim($datas[$i][5]);
            if( $status == "Registered" ){
                $customer_id = trim($emails_contact[$email]);
                $customer_id = ($customer_id == "" || $customer_id == null) ? "": $customer_id;
                if( $customer_id != ""){
                    if( $value_string != "" ){
                        $value_string .=","."(".$customer_id.",'".$email."','".$datas[$i][6]."','".$datas[$i][7]."','".$datas[$i][9]."','".$datas[$i][10]."','".$user_name."','"."INITIATED"."')";
                    } else {
                        $value_string = "(".$customer_id.",'".$email."','".$datas[$i][6]."','".$datas[$i][7]."','".$datas[$i][9]."','".$datas[$i][10]."','".$user_name."','"."INITIATED"."')";
                    }
                }
            }
        }

        $insert_qry = "INSERT INTO monthly_bill_call_list( contact_id,email,charging_due_date,next_renewal_date,package_price,due_amount,generated_by,`status`)
                       VALUES ".$value_string.";";
        if( $value_string != "" ){
            try{
                $rs = Sql_exec($cn,$insert_qry);
                if( $rs ){
                    echo json_encode(array(
                        "status"=> 1,
                        "msg"=>"Call list Generation Successful for $renew_date Date"
                    ));
                }
            }catch (Exception $e){

            }
        }
        unset($datas);
    }
}else{
    echo json_encode(array(
        "status"=> 0,
        "msg"=>"Failed to generate call list for $renew_date Date"
    ));
}



ClosedDBConnection($cn);