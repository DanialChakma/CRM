<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 8/17/2016
 * Time: 4:03 PM
 */

require_once "../lib/common.php";
require_once "fin_lib.php";
$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;
$user_name = trim($_SESSION['user_name']);
$cn = connectDB();
$bill_id = mysql_real_escape_string(trim($datainfo['bill_id']));
$mb_id = mysql_real_escape_string(trim($datainfo['mb_id']));
$remarks = mysql_real_escape_string(trim($datainfo['remarks']));
$state = mysql_real_escape_string(trim($datainfo['state']));
if( empty($state) || $state != "Reject" ){
    $state = "Reject";
}

$collected_amount = 0.0;
$delivery_cost = 0.0;
$total_receive = 0.0;// total receive must be (collected amount + delivery cost)
$collection_date = date('Y-m-d');
$collection_time = date('H:i:s');

$url_to_hit = "http://fin.ssd-tech.com/dozecrm/CommonService.asmx/CollectionStatusChange";


$data = array(
    "TransactionID"=>$bill_id,
    "State"=>$state,
    "Receipt"=>"",
    "CollectDate"=>$collection_date,
    "CollectTime"=>$collection_time,
    "CollectPrice"=>$collected_amount,
    "OtherCost" =>$delivery_cost,
    "User"=>$user_name
);

$param = http_build_query($data);
$url_with_param = $url_to_hit."?".$param;
$current_time_stamp = date('Y-m-d H:i:s');
$client_ip = get_client_ip();
$request_param_string = json_encode($data);

$req_method = "GET";

$response = curl_request($url_to_hit,$req_method,$data);
$xml = simplexml_load_string($response);
$res = (string)$xml[0];
$res = strtolower($res);


$response_string = mysql_real_escape_string($res);
$request_qry = "INSERT INTO remote_request_log (request_url,request_method,request_param,request_result,request_host_ip,login_user,request_time )VALUES ( '$url_with_param', '$req_method', '$request_param_string', '$response_string', '$client_ip', '', '$current_time_stamp');";
if( $res === "+ok" ){
    $qry = "UPDATE monthly_bill_call_list
                SET
                  invoice_book_id = '',
                  receipt_number = '',
                  remarks = '$remarks',
                  cash_receive = '$collected_amount',
                  delivery_cost = '$delivery_cost',
                  total_receive = '$total_receive'
                WHERE id='$mb_id' AND transaction_id='$bill_id'";
    Sql_exec($cn,$qry);

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
            "msg"=>"Failed to Update Bill."
        )
    );

}

Sql_exec($cn,$request_qry);

ClosedDBConnection($cn);


