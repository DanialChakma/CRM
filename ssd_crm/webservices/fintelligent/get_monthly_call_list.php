<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 9/18/2016
 * Time: 1:16 PM
 */



require_once "../lib/common.php";
$user_name = trim($_SESSION['user_name']);
$user_role = trim($_SESSION['user_role']);
date_default_timezone_set("Asia/Dhaka");
$current_date = date("Y-m-d");
$condition = "";
if( $user_role == "account_admin" ){
   // account_admin will be able to see all the calling task
   // So, no where condition
    $condition = "WHERE DATE(mb.call_date)='".$current_date."' AND mb.`status` != 'PAID'";
}else{
    // Other user will see only assigned calling task.
    $condition = "WHERE mb.assign_to='".$user_name."' AND DATE(mb.call_date)='".$current_date."' AND mb.`status` != 'PAID'";
}
$cn = connectDB();

$customer_data = array();
$qry = "SELECT email,customer_name,phone_no_p FROM cgw_customers GROUP BY email";
$rs = Sql_exec($cn,$qry);
while( $dt = Sql_fetch_array($rs) ){
    $customer_data[trim($dt['email'])] = array(
                                                "f_name" => trim($dt['customer_name']),
                                                "contact_no" => $dt['phone_no_p']
                                            );
}


$qry = "SELECT  mb.email,mb.id AS mbcl_id,
                mb.package_price AS pkg_price,
                mb.package_price_tax AS pkg_price_tax,
                mb.due_amount AS due_amount
        FROM monthly_bill_call_list mb ".$condition.";";

$rs = Sql_exec($cn,$qry);

$data = array();
$i = 0;

while( $dt = Sql_fetch_array($rs) ){
    $j=0;
    $contact=trim($dt['email']);
    $customer_name = $customer_data[$contact]['f_name'];
    $mb_clid = trim($dt['mbcl_id']);

    $package_price = trim($dt['pkg_price']);
    $package_price_tax = trim($dt['pkg_price_tax']);

    $package_price_total = floatval($package_price) + floatval($package_price_tax);
    $package_price_total = round($package_price_total,4);

    $qry_h = "SELECT COUNT(*) AS 'row' FROM monthly_bill_call_list_history WHERE mbcl_id ='$mb_clid' AND call_status='Connected' AND DATE(call_date) = DATE(NOW());";
    $rs_h = Sql_exec($cn,$qry_h);
    $row = Sql_fetch_array($rs_h);
    $row_count = $row['row'];
    Sql_Free_Result($rs_h);
    $is_called_str = "";
    if( $row_count > 0 ){
        $is_called_str = "<span class=\"glyphicon glyphicon-ok-circle\"></span></p>";
    }else{
        $is_called_str = "<span class=\"glyphicon glyphicon-remove-circle\"></span>";
    }

    if( empty($customer_name) ){
        $customer_name = $contact;
    }
    $hidden_str = '<input type="hidden" value="'.$dt['id'].'"/>';
    $customer_mbid_str ="'".$contact."|".$dt['mbcl_id']."'";
    $html = '<span onclick="show_monthly_details('.$customer_mbid_str.')">'.$customer_name.'</span>';
    $call_history = '<a style="cursor:poiner;"class="btn btn-primary" onclick="show_call_history('.trim($dt['mbcl_id']).')">'."show callhistory".'</a>';

    $data[$i][$j++] = $is_called_str;
    $data[$i][$j++] = $hidden_str.$html;
    $data[$i][$j++]= $contact;
    $data[$i][$j++]= $customer_data[$contact]['contact_no'];
    $data[$i][$j++]= $package_price_total;
    $data[$i][$j++]= number_format(trim($dt['due_amount']),2);
    $data[$i][$j++]= $call_history;
    $i++;
}

echo json_encode($data);

ClosedDBConnection($cn);