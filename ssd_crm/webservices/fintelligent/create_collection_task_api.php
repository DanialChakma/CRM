<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 11/23/2016
 * Time: 6:07 PM
 */

require_once "../lib/common.php";
require_once "fin_lib.php";
date_default_timezone_set("Asia/Dhaka");

$mbcl_id = trim($_REQUEST['mbcl_id']);
$feedback = trim($_REQUEST['feedback']);
if( empty($mbcl_id) || empty($feedback) ){
    echo "err"."|"."Parameter Missing";
    exit;
}

$url_to_hit = "http://fin.ssd-tech.com/dozecrm/CommonService.asmx/ParcelEntry";
$transaction_id = substr(uniqid("DB",true),0,16);

$cn = connectDB();

$bill_inserted = "Support Subscriber";

$call_status = "Connected";
$outcome = "3";
$payment_date = date("Y-m-d")." 00:00:00";
$current_date_time =  date("Y-m-d H:i:s");
$payment_mode = "3";
$user_name = "account_admin";


$mb_qry = "SELECT contact_id,package_price,due_amount
           FROM monthly_bill_call_list WHERE `id`='$mbcl_id';";
$rs = Sql_exec($cn,$mb_qry);
$mb_dt = Sql_fetch_array($rs);
$due_amount = floatval($mb_dt['due_amount']);
$due_amount = empty($due_amount)? 0.0:$due_amount;
$contact_id = $mb_dt['contact_id'];

$qry = "SELECT
                 CONCAT_WS(\" \", contact.first_name,contact.last_name ) AS customer_name,
                 contact.email AS email,
                 CONCAT_WS(\", \", NULLIF(contact.phone1, \"\"), NULLIF(contact.phone2, \"\")) AS phone,
                 contact.address1 AS address1,
                 contact.address2 AS address2,
                 convertion.real_ip_cost AS real_ip_cost,
                 convertion.collection_amount AS total_cost,
                 convertion.package AS package
        FROM
              contacts AS contact INNER JOIN customer_conversion AS convertion
              ON  contact.id = convertion.contact_id
        WHERE contact.id ='".$contact_id."';";

$rs = Sql_exec($cn,$qry);
$dt = Sql_fetch_array($rs);

$payment_collection_address = trim($dt['address2']);
$net_connection_address = trim($dt['address1']);
$customer_name = trim($dt['customer_name']);
$phone_number = trim($dt['phone']);
$package = trim($dt['package']);
$email = trim($dt['email']);
$real_ip_cost = floatval($dt['real_ip_cost']);
$real_ip_cost = empty($real_ip_cost)? 0.0:$real_ip_cost;

$total_cost = $due_amount + $real_ip_cost;
$data = array(

    'CustID'        => $contact_id,
    'TransactionID' =>$transaction_id,
    'PaymentType' => "MonthlyBill",
    'Name'     => $customer_name,
    'Mobile'   => $phone_number,
    'EmailID'  => $email,
    'PaymentCollectionAddress'=> $payment_collection_address,
    'Package'      => $package,

    'OriginalCost' => 0,
    'RealIPCost'   => $real_ip_cost,
    'OtherCost'    => 0,

    'MonthlyBill' =>$due_amount,
    'Months'      => 1,
    'TotalCost'   => $total_cost,

    'CollectDate' => $payment_date,
    'CollectTime' =>'',
    'CollectedCost'=>0,
    'ConnectionAddress' => $net_connection_address,
    'CollectDO'=>'',
    'BillInserter'=> $bill_inserted,
    'Remark'=>'',
    'ReceiptNO'=>'',
    'Agent'=> $user_name
);

$err_status = 1;
$curl_response = curl_request($url_to_hit,"GET",$data);
$xml = simplexml_load_string($curl_response);
$res = (string)$xml[0];
$res = strtolower($res);
if($res == "+ok"){
    $err_status = 0;
}else{
    $err_status = 2;
}

$monthly_call_qry = "UPDATE monthly_bill_call_list
                             SET  `transaction_id` = '$transaction_id',
                                  `update_count` = IFNULL(update_count, 0)+1,
                                  `payment_date` = '$payment_date',
                                  `status` = '$outcome',
                                  `remarks` = '$feedback',
                                  `collection_date`='$payment_date'
                      WHERE `contact_id` = '$contact_id' AND `id` = '$mbcl_id';";

$mb_history_qry = "INSERT INTO monthly_bill_call_list_history(mbcl_id,contact_id,agent_name,feedback,call_date,call_status,call_outcome,payment_method,payment_date
                   ) VALUES (
                            '$mbcl_id',
                            '$contact_id',
                            '$user_name',
                            '$feedback',
                            '$current_date_time',
                            '$call_status',
                            '$outcome',
                            '$payment_mode',
                            '$payment_date'
                    );";

if( $err_status == 0 ){
    $rs = Sql_exec($cn,$monthly_call_qry);
    if($rs){
        $mb_rs = Sql_exec($cn,$mb_history_qry);
        if( !$mb_rs ){
            $err_status = 4;
        }
    }else{
            $err_status = 3;
    }
}

if( $err_status == 0 ){
    echo "ok"."|"."Operation Successful.";
}else{
    if( $err_status == 2 ){
        echo "err"."|"."Error in Doze Bill Insertion.";
    }elseif( $err_status == 3 ){
        echo "err"."|"."Error in Monthly Call List Update.";
    }elseif($err_status == 4){
        echo "err"."|"."Error in Monthly Call List History Insertion";
    }else{
        echo "err"."|"."Unknown Error.";
    }
}

ClosedDBConnection($cn);