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
$params = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;
$user_name = trim($_SESSION['user_name']);

$cn = connectDB();
$collected_amount_u = mysql_real_escape_string(trim($params['collected_amount']));
$delivery_cost_u = mysql_real_escape_string(trim($params['delivery_cost']));
$installation_cost_u = mysql_real_escape_string(trim($params['installation_cost']));
$receipt_number_u = mysql_real_escape_string(trim($params['receipt_number']));
$collected_amount_u = empty($collected_amount_u) ? 0.0: floatval($collected_amount_u);
$delivery_cost_u    = empty($delivery_cost_u) ? 0.0: floatval($delivery_cost_u);
$installation_cost_u    = empty($installation_cost_u) ? 0.0: floatval($installation_cost_u);
$total_receive = round(($collected_amount_u+$delivery_cost_u+$installation_cost_u),4);

$mb_id = mysql_real_escape_string(trim($params['mb_id']));
$qry = "SELECT email,cash_receive,receipt_number,package_price,due_amount,payment_date,delivery_cost,collection_status
        FROM monthly_bill_call_list
        WHERE `id` = '$mb_id';";
$rs = Sql_exec($cn,$qry);
$dt = Sql_fetch_array($rs);

$email = trim($dt['email']);
//$cash_receive = floatval($dt['cash_receive']);
//$delivery_cost = floatval($dt['delivery_cost']);
$collection_status = $dt['collection_status'];

if( $collection_status === "yes" ){

    $update_qry = "UPDATE monthly_bill_call_list SET cash_receive='$collected_amount_u',`installation_cost`='$installation_cost_u',delivery_cost='$delivery_cost_u',total_receive='$total_receive',receipt_number='$receipt_number_u', `collection_status`='confirm' WHERE `id`='$mb_id';";
    $rs_up = Sql_exec($cn,$update_qry);
    if($rs_up){
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
                "msg"=>"Operation Failed"
            )
        );
    }


}else{
    echo json_encode(
        array(
            "code"=>2,
            "msg"=>"Collection Status State Should be \"yes\" to initiate your desired Action."
        )
    );
}



ClosedDBConnection($cn);


