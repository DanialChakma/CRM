<?php
/*
 *
Edited by Talemul Islam
*/
require_once "../lib/common.php";
checkSession();
$user_id = $_SESSION['user_id'];
$cn = connectDB();

if (isset($_REQUEST)) {
    $action = $_REQUEST["action"];
    $action_id = $_REQUEST["action_id"];

    $conversion_update_id = $_REQUEST["conversion_update_id"];
    $conversion_date = $_REQUEST["conversion_date"];
    $conversion_agent = $_REQUEST["conversion_agent"];
    $conversion_status = $_REQUEST["conversion_status"];
    $conversion_note = $_REQUEST["conversion_note"];
    $client_type = $_REQUEST["client_type"];
    $install_cost = $_REQUEST["install_cost"];
    $real_ip_cost = $_REQUEST['real_ip_charge'];
    $additional_cost = $_REQUEST['additional_charge'];
    $monthly_cost = $_REQUEST["monthly_cost"];
    $month_number = $_REQUEST["month_number"];
    $month_number = (!isset($month_number) || $month_number == null || $month_number == '') ? 0 : $month_number;
    $collection_amount = $install_cost + $monthly_cost * $month_number;
    $package = $_REQUEST["package"];
    $collection_note = $_REQUEST["collection_note"];
    $assignment_date = $_REQUEST["assignment_date"];
    $collection_date = $_REQUEST["conversion_collection_date"] . ' ' . $_REQUEST["conversion_collection_time"];
    $date_time = isset($_REQUEST["date_time"]) ? $_REQUEST["date_time"] : date('Y-m-d H:i:s');

    $payment_mode = trim($_REQUEST["payment_mode_conversion"]);
    $payment_type = trim($_REQUEST["payment_type"]);
}

if (trim($conversion_date) == '' || $conversion_date == null) {
    $conversion_date = date('Y-m-d h:i:s');
}

if (trim($assignment_date) == '' || $assignment_date == null) {
    $assignment_date = date('Y-m-d h:i:s');
}

$is_error = 0;

$select_qry = "select count(*) as `count` from customer_conversion where contact_id='$conversion_update_id'";

$result = Sql_exec($cn, $select_qry);

$count = 0;

while ($data = Sql_fetch_array($result)) {
    $count = $data['count'];
}

if ( $count == 0 || trim($count) == '0' ) {
    $contact_qry = "insert into customer_conversion
                    (contact_id,conversion_date,conversion_agent,conversion_status,conversion_note,client_type,collection_amount,install_cost,monthly_cost,real_ip_cost,additional_cost,month_number,package,collection_note,assignment_date,collection_date,update_date,update_by,payment_type,payment_mode)
                    values('$conversion_update_id','$conversion_date','$conversion_agent','$conversion_status','$conversion_note','$client_type','$collection_amount','$install_cost','$monthly_cost','$real_ip_cost','$additional_cost','$month_number','$package','$collection_note','$assignment_date','$collection_date',NOW(),'" . $user_id . "','$payment_type','$payment_mode')";
} else {
    $contact_qry = "update customer_conversion set payment_type='$payment_type',payment_mode='$payment_mode', conversion_date='$conversion_date',conversion_agent='$conversion_agent',conversion_status='$conversion_status',conversion_note='$conversion_note',client_type='$client_type',collection_amount='$collection_amount',install_cost='$install_cost',monthly_cost='$monthly_cost',`real_ip_cost`='$real_ip_cost',`additional_cost`='$additional_cost',month_number='$month_number',package='$package',collection_note='$collection_note',assignment_date='$assignment_date',collection_date='$collection_date', update_date= NOW(),update_by='" . $user_id . "' where contact_id='$conversion_update_id'";
}

try {
    $res = Sql_exec($cn, $contact_qry);
   /*
    $transaction_qry = "SELECT conv.collection_amount,
                               conv.install_cost,
                               conv.monthly_cost,
                               conv.month_number,
                               conv.real_ip_cost,
                               conv.additional_cost,
                               conv.package,
                               conv.collection_date,
                               conv.transaction_id,
                               CONCAT_WS(\" \",IFNULL( contact.first_name ,\"\"),IFNULL( contact.last_name ,\"\")) as cutomer_name,
                               CONCAT( IFNULL( contact.phone1 ,\"\"),IFNULL( contact.phone2 ,\"\")) as phone,
                               contact.email,
                               contact.address1 as connection_address,
                               contact.address2 as collection_address,
                               contact.do_area  as  do_area
                        FROM customer_conversion as conv INNER JOIN contacts as contact ON conv.contact_id=contact.id
                        WHERE contact_id = '$conversion_update_id'";
    $res = Sql_exec($cn, $transaction_qry);
    $dt = Sql_fetch_array($res);
    $bill_id = trim($dt['transaction_id']);
    $collection_date = trim($dt['collection_date']);
    $date_time_array = explode(" ",$collection_date);
    $date = $date_time_array[0];
    $time = $date_time_array[1];

    if( !empty($bill_id) ){
        $data = array(
                        "CustID"=>$conversion_update_id,
                        "TransactionID"=>$bill_id,
                        "Name"=>trim($dt['cutomer_name']),
                        "Mobile"=>trim($dt['phone']),
                        "EmailID"=>trim($dt['email']),
                        "PaymentCollectionAddress"=>trim($dt['collection_address']),
                        "Package"=>trim($dt['package']),
                        "OriginalCost"=>trim($dt['install_cost']),
                        "RealIPCost"=>trim($dt['real_ip_cost']),
                        "OtherCost"=>trim($dt['additional_cost']),
                        "TotalCost"=>trim($dt['collection_amount']),
                        "CollectDate"=>trim($date),
                        "CollectTime"=>trim($time),
                        "ConnectionAddress"=>trim($dt['connection_address']),
                        "CollectDO"=>trim($dt['do_area']),
                        "MonthlyBill"=>trim($dt['monthly_cost']),
                        "Months"=>trim($dt['month_number'])
                 );
        $fint_bill_initiate = "http://fin.ssd-tech.com/dozecrm/CommonService.asmx/ParcelModify";
        $param = http_build_query($data);
        $url_to_hit = $fint_bill_initiate."?".$param;
        $req_method = "GET";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($ch, CURLOPT_URL, $url_to_hit);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,$req_method);
        $response = "";
        if( ($response = curl_exec($ch))=== FALSE ){
            // echo "ERROR:". curl_error($ch);
        }else{
            //  echo "Server Res:".$response;
        }
        $xml = simplexml_load_string($response);
        $res = (string)$xml[0];
        $res = strtolower($res);
        $request_param_string = json_encode($data);
        if( curl_errno($ch) )
        {
            $is_error = 1;
            $request_qry = "INSERT INTO `remote_request_log` ( request_url,request_method,request_param,request_result,login_user )
                            VALUES ( '$url_to_hit', '$req_method', '$request_param_string', '$res','$user_id');";
            Sql_exec($cn,$request_qry);
        }else{
            $response_string = mysql_real_escape_string($res);
            $request_qry = "INSERT INTO `remote_request_log` ( request_url,request_method,request_param,request_result,login_user )
                            VALUES ( '$url_to_hit', '$req_method', '$request_param_string', '$res','$user_id');";
            if( $res === "+ok" ){

            }else{
                $is_error = 1;
            }

            Sql_exec($cn,$request_qry);
        }

        curl_close($ch);

    } */

} catch (Exception $e) {
    $is_error = 1;
}

ClosedDBConnection($cn);

echo $is_error;
