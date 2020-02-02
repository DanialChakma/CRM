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
$mb_id = mysql_real_escape_string(trim($params['mb_id']));
$collected_amount = mysql_real_escape_string(trim($params['collected_amount']));
$delivery_cost = mysql_real_escape_string(trim($params['delivery_cost']));
$remarks = mysql_real_escape_string(trim($params['remarks']));

$collected_amount   = empty($collected_amount) ? 0.0:floatval($collected_amount);
$delivery_cost      = empty($delivery_cost) ? 0.0 :floatval($delivery_cost);
$total_receive = round(($collected_amount + $delivery_cost),4);

$collection_date = date('Y-m-d');
$collection_time = date('H:i:s');
$collection_status = "yes";
$qry = "UPDATE monthly_bill_call_list
            SET
                  remarks = '$remarks',
                  cash_receive = '$collected_amount',
                  delivery_cost = '$delivery_cost',
                  total_receive = '$total_receive',
                  collection_status = '$collection_status',
                  collection_date = '$collection_date'
            WHERE id='$mb_id'";
$rs = Sql_exec($cn,$qry);
if($rs){
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


ClosedDBConnection($cn);


