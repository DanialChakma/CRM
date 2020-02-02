<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 3/22/2017
 * Time: 1:24 PM
 */


require_once "../lib/common.php";
$agent = (isset($_REQUEST['agent'])) ? $_REQUEST['agent'] : exit;
date_default_timezone_set("Asia/Dhaka");
$agent = trim($agent);
$cn = connectDB();

$customers = array();
$qry = "SELECT customer_name, email, phone_no_p,present_address_1,package
        FROM cgw_customers;";
$rs = Sql_exec($cn,$qry);
while($dt=Sql_fetch_array($rs)){
    $email = trim($dt['email']);
    $customers[$email] = array(
        "customer_name"=>$dt['customer_name'],
        "phone_no"=>$dt['phone_no_p'],
        "address"=>$dt['present_address_1'],
        "package" => $dt['package']
    );
}

Sql_Free_Result($rs);

$lower_range = date('Y-m-d',strtotime("-1 day"))." "."00:00:00";
$upper_range = date('Y-m-d',strtotime("+1 day"))." "."59:59:59";

$qry = "SELECT `id`,radius_user_name,email,package_price,package_price_tax,due_amount,collection_date,remarks,bill_type FROM monthly_bill_call_list
        WHERE `collection_status`='no' AND `collection_agent`='$agent' AND `payment_method` = '3' AND `status` <> 'PAID'
        AND collection_date >= '$lower_range' AND collection_date <= '$upper_range';";
$rs = Sql_exec($cn,$qry);

$day_maps = array(
    date('Y-m-d') => "Today",
    date('Y-m-d',strtotime("+1 day")) => "Tomorrow",
    date('Y-m-d',strtotime("-1 day")) =>"Yesterday"
);

$dataSet = array();
while($dt=Sql_fetch_array($rs)){

    $radius_id = intval($dt['radius_user_name']);
    $bill_type = trim($dt['bill_type']);
    $installation_cost = 0.00;
    $real_ip_cost = 0.00;
    $additional_cost = 0.00;
    if( !empty($radius_id) && $bill_type == "DB" ){
        $qry = "SELECT real_ip_cost,additional_cost,install_cost
                FROM customer_conversion WHERE contact_id IN (SELECT id FROM contacts WHERE radius_user='$radius_id');";
        $rs_ipCost = Sql_exec($cn,$qry);
        $dt_ip=Sql_fetch_array($rs_ipCost);
        $real_ip_cost_db = $dt_ip['real_ip_cost'];
        if( !empty($real_ip_cost_db) ){
            $real_ip_cost = floatval($real_ip_cost_db);
        }

        $installation_cost = trim($dt_ip['install_cost']);
        if( !empty($installation_cost) ){
            $installation_cost = floatval($installation_cost);
        }
        $additional_cost = trim($dt_ip['additional_cost']);
        if( !empty($additional_cost) ){
            $additional_cost = floatval($additional_cost);
        }
    }

    $email = trim($dt['email']);
    $time = date('h:i A', strtotime($dt['collection_date']));
    $deliv_time = date("F j, Y",strtotime($dt['collection_date']));
    $relative_day = "";
    $key = date('Y-m-d',strtotime($dt['collection_date']));
    if( array_key_exists($key,$day_maps) ){
        $relative_day = $day_maps[$key];
    }

    $package_price_tax = floatval(trim($dt['package_price_tax']));
    $package_price = floatval(trim($dt['package_price']));
    $monthly_cost = round(($package_price_tax + $package_price),4);
    $other_cost = $additional_cost + $real_ip_cost;
    $dataSet[] = array(
        "ID" =>trim($dt['id']),
        "BillType" =>trim($dt['bill_type']),
        "Email"=>$email,
        "Phone"=>$customers[$email]['phone_no'],
        "sender"=>$customers[$email]['customer_name'],
        "Address" => $customers[$email]['address'],
        "Package" => $customers[$email]['package'],
        "MonthlyCost"=> $monthly_cost,
        "OtherCost" =>$other_cost,
        "TotalCost" => round(($monthly_cost+$other_cost),4),
        "InstallCost" => round(($installation_cost),4),
        "DueAmount"=>floatval($dt['due_amount']),
        "delivTime" => $time,
        "delivDate" => $deliv_time,
        "remarks" => trim($dt['remarks']),
        "delivDateNoun" =>$relative_day
    );
}

ClosedDBConnection($cn);

echo json_encode($dataSet);