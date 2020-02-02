<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 8/17/2016
 * Time: 4:03 PM
 */

require_once "../lib/common.php";
require_once "fin_lib.php";
date_default_timezone_set("Asia/Dhaka");
$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;
$user_name = trim($_SESSION['user_name']);

$cn = connectDB();
$bill_id = mysql_real_escape_string(trim($datainfo['bill_id']));
$customer_id = mysql_real_escape_string(trim($datainfo['customer_id']));
$cheque_book_id = mysql_real_escape_string(trim($datainfo['cheque_book_id']));
$cheque_book_page = mysql_real_escape_string(trim($datainfo['cheque_book_page']));
$remarks = mysql_real_escape_string(trim($datainfo['remarks']));
$nid     = mysql_real_escape_string(trim($datainfo['nid']));
$photo   = mysql_real_escape_string(trim($datainfo['photo']));
$sap     = mysql_real_escape_string(trim($datainfo['sap']));

$collected_price = 0;

$collection_date = date('Y-m-d');
$collection_time = date('H:i:s');

$url_to_hit = "http://fin.ssd-tech.com/dozecrm/CommonService.asmx/CollectionStatusChange";


$data = array(
    "TransactionID"=>$bill_id,
    "Receipt"=>"",
    "CollectPrice"=>$collected_price,
    "CollectDate"=>$collection_date,
    "CollectTime" =>$collection_time,
    "State"=>"Reject",
    "User"=>$user_name
);

$param = http_build_query($data);
$url_with_param = $url_to_hit."?".$param;
$current_time_stamp = date('Y-m-d H:i:s');
$client_ip = get_client_ip();
$request_param_string = json_encode($data);

$req_method = "GET";
$response=curl_request($url_to_hit,$req_method,$data);
$xml = simplexml_load_string($response);
$res = (string)$xml[0];
$res = strtolower($res);

$response_string = mysql_real_escape_string($res);
$request_qry = "INSERT INTO remote_request_log (request_url,request_method,request_param,request_result,request_host_ip,login_user,request_time )VALUES ( '$url_with_param', '$req_method', '$request_param_string', '$response_string', '$client_ip', '', '$current_time_stamp');";
if( $res === "+ok" ){

    $qry = "SELECT count(contact_id) as num, remarks FROM payments
            WHERE contact_id = '$customer_id';";
    $rs = Sql_exec($cn,$qry);
    $dt = Sql_fetch_array($rs);
    $row_num = intval($dt['num']);
    $remarks_db = trim($dt['remarks']);
    if( !empty($remarks_db) ){
        $remarks_db .= "\n".date('Y-m-d H:i:s')."=>".$remarks;
    }else{
        $remarks_db = date('Y-m-d H:i:s')."=>".$remarks;
    }

    $payment_mode = "Ecourier";
    $collection_status = "open";
    $payment_type = "Cash";
        if( $row_num > 0 ){

                $qry = "UPDATE payments AS pay
                    SET
                        pay.collection_status='$collection_status',
                        pay.payment_type='$payment_type',
                        pay.cash_receive ='$collected_price',
                        pay.receipt_bookid='',
                        pay.receipt_number='',
                        pay.collection_date='$collection_date',
                        pay.collected_by='$user_name',
                        pay.payment_mode='$payment_mode',
                        pay.nid='',
                        pay.photo='',
                        pay.saf='',
                        pay.remarks = '$remarks_db'
                WHERE pay.contact_id = '$customer_id';";

                Sql_exec($cn,$qry);
        }


        $qry_cheque_status = "UPDATE chequebooks
                              SET `Status` = 'NEW'
                              WHERE InvoiceBookID = '$cheque_book_id' AND InvoiceNo='$cheque_book_page';";
        Sql_exec($cn,$qry_cheque_status);
        echo json_encode(
            array(
                "code"=>1,
                "msg"=>"Operation Successful"
            )
        );

}else{
        echo json_encode(
            array(
                "code"=>2,
                "msg"=>"URL Successfully Hitted.But,Failed to Update Bill."
            )
        );

}

Sql_exec($cn,$request_qry);
ClosedDBConnection($cn);


