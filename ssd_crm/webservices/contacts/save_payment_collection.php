<?php

/*
 *
Edited by Talemul Islam
*/
require_once "../lib/common.php";
checkSession();
$user_id = $_SESSION['user_id'];

$cn = connectDB();
//var_dump($_REQUEST); exit;


if(isset($_REQUEST)){
    $action = $_REQUEST["action"];
    $action_id = $_REQUEST["action_id"];
    $contact_id = $_REQUEST["payment_contact_id"];
    $collection_date = $_REQUEST["collection_date"];
    $doze_id = $_REQUEST["payment_doze_id"];
    $collected_by = $_REQUEST["collected_by"];
    $receipt_number = $_REQUEST["receipt_number"];
    $payment_mode = $_REQUEST["payment_mode"];
    $collection_status = $_REQUEST["collection_status"];
    $date_time = isset($_REQUEST["date_time"]) ? $_REQUEST["date_time"] : date('Y-m-d H:i:s');
    $saf = trim($_REQUEST["sap"]);
    $photo = trim($_REQUEST["photo"]);
    $nid = trim($_REQUEST["nid"]);
    $remarks = mysql_real_escape_string(trim($_REQUEST["remarks"]));
}


$is_error = 0;

$select_qry = "select count(*) as `count` from payments where contact_id='$contact_id'";

$result = Sql_exec($cn, $select_qry);

$count = 0;

while($data = Sql_fetch_array($result)){
    $count = $data['count'];
}

if(($count ==0 || trim($count) =='0') ) {
    $contact_qry = "insert into payments(collection_date,doze_id,collection_status,payment_mode,receipt_number,collected_by,contact_id,update_date,update_by,remarks,nid,photo,saf ) values('$collection_date','$doze_id','$collection_status','$payment_mode','$receipt_number','$collected_by','$contact_id', NOW(),'".$user_id."','$remarks','$nid','$photo','$saf')";
}else {
    $contact_qry = "update payments set collection_date='$collection_date',doze_id='$doze_id',collection_status='$collection_status',payment_mode='$payment_mode',receipt_number='$receipt_number',collected_by='$collected_by', update_date= NOW(),update_by='".$user_id."',remarks='$remarks',nid='$nid',photo='$photo',saf='$saf' where contact_id='$contact_id'";
}

try {
    $res = Sql_exec($cn, $contact_qry);

    $is_bill_qry = "";

} catch (Exception $e) {
    $is_error = 1;
}


ClosedDBConnection($cn);

echo $is_error;
