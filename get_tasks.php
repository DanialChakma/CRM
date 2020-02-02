<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 8/11/2016
 * Time: 8:09 PM
 */

require_once "ssd_crm/webservices/lib/common.php";
$cn = connectDB();
/*
$qry = "SELECT 	id, contact_id, conversion_date, conversion_agent,install_cost, collection_amount,monthly_cost,month_number, package,collection_note, transaction_id
        FROM customer_conversion LIMIT 0,50"; */
$uname = $_REQUEST['un'];
$pass =   $_REQUEST['pass'];
echo $uname;
echo "<br/>";
echo $pass;		
$qry = "select * from user_info where user_name='$uname' AND user_password='$pass';";
echo $qry;
$rs = Sql_exec($cn,$qry);

$row=Sql_fetch_array($rs);
echo "<pre>";
print_r($row);
echo "</pre>";
$data = array();
/*
while($row=Sql_fetch_array($rs)){
    $data[] = array(
        "id"=>$row['id'],
        "contact_id"=>$row['contact_id'],
        "conversion_date"=>$row['conversion_date'],
        "install_cost"=>$row['install_cost'],
        "monthly_cost"=>$row['monthly_cost']
    );
} */
ClosedDBConnection($cn);
/*
echo "<pre>";
echo print_r($data);
echo "</pre>";
*/