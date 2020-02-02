<?php

require_once "../lib/common.php";
require_once "fin_lib.php";
//session_start();
//$user_name = $_SESSION['user_name'];
//$user_role = $_SESSION['user_role'];

$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;
$start_date = trim($datainfo['start_date']);
$end_date = trim($datainfo['end_date']);
$start_date = $start_date ? $start_date:"";
$end_date   = $end_date ? $end_date:"";
$user_role = "";

$current_time_stamp = date('Y-m-d H:i:s');
$cn = connectDB();
$cgw_customer_info = array();
$qry_cgw_customers = "SELECT TRIM(email) AS email, customer_name, phone_no_p,package,present_address_1 FROM cgw_customers GROUP BY email;";
$rs = Sql_exec($cn,$qry_cgw_customers);
while( $dt = Sql_fetch_array($rs) ){
        $cgw_customer_info[$dt['email']] = array(
                                                "customer_name" => trim($dt['customer_name']),
                                                "contact_no" => trim($dt['phone_no_p']),
                                                "package" => trim($dt['package']),
                                                "address" =>trim($dt['present_address_1'])
                                            );
}

Sql_Free_Result($rs);
$dataset = array();
$qry = "SELECT id, email,due_amount,collection_status,collection_agent FROM monthly_bill_call_list WHERE `payment_method` ='3' AND `status` <> 'PAID' AND payment_date BETWEEN '$start_date' AND '$end_date';";

$rs = Sql_exec($cn,$qry);
$i=0;
while( $dt=Sql_fetch_array($rs) ){
    $j=0;
    $check_box_string = "<input type=\"checkbox\" value=\"".$dt['id']."\"/>";

    $dataset[$i][$j++] = $check_box_string;
    $dataset[$i][$j++] = '<span href="#" onclick="view_manager_task_details(\''.$dt['id'].'\')">'. $cgw_customer_info[$dt['email']]['customer_name'].'</span>';
    $dataset[$i][$j++] = $dt['email'];
    $dataset[$i][$j++] = $cgw_customer_info[$dt['email']]['contact_no'];
    $dataset[$i][$j++] = $cgw_customer_info[$dt['email']]['address'];
    $dataset[$i][$j++] = $dt['due_amount'];
    $dataset[$i][$j++] = $dt['collection_status'];
    $dataset[$i][$j++] = empty( $dt['collection_agent'] ) ? "": trim( $dt['collection_agent'] );
    $i++;
}

ClosedDBConnection($cn);
echo json_encode($dataset);


