<?php

require_once "../lib/common.php";
require_once "fin_lib.php";
//session_start();

$user_name = $_SESSION['user_name'];
//$user_role = $_SESSION['user_role'];
$cn = connectDB();


 $qry = "SELECT contact.customer_name AS customer_name,
               contact.phone_no_p AS phone,
               mb.collection_date as collection_date,
               mb.bill_type AS bill_type,
               mb.id AS id,
               mb.collection_status AS collection_status
        FROM monthly_bill_call_list AS mb INNER JOIN cgw_customers AS contact ON mb.email = contact.email
        WHERE collection_agent='$user_name';";



$dataset = array();
$rs = Sql_exec($cn,$qry);
$i = 0;
while($dt = Sql_fetch_array($rs)){
    $j = 0;
    $customer_name = trim($dt['customer_name']);
    $bill_id = trim($dt['id']);
    $bill_type = trim($dt['bill_type']);
    $status = trim($dt['collection_status']);

    $action_string = '<input type="hidden" id="'.$bill_id.'" value="'.$bill_type.'"/>';
    $action_string .= '<input type="hidden" id="'.$bill_id."bill_type".'" value="'.$bill_type.'"/>';
    $action_string .= '<span href="#" onclick="view_account_task_details(\''.$bill_id.'\')">'.$customer_name.'</span>';
    $dataset[$i][$j++] = $action_string;
    $dataset[$i][$j++] = trim($dt['phone']);
    $dataset[$i][$j++] = trim($dt['collection_date']);
    $dataset[$i][$j++] = ( $bill_type == "MDB" ? "Monthly Bill":"Installation Bill" );
    $dataset[$i][$j++] = $status;
    $i++;
}

echo json_encode($dataset);
ClosedDBConnection($cn);
