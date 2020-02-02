<?php
/**
 * Created by PhpStorm.
 * User: L440-User
 * Date: 8/11/2016
 * Time: 7:16 PM
 */

require_once "../lib/common.php";

$cn = connectDB();

$qry = "SELECT 	id, contact_id, conversion_date, conversion_agent,install_cost, collection_amount,monthly_cost,month_number, package,collection_note, transaction_id
        FROM customer_conversion LIMIT 0,12";

$rs = Sql_exec($cn,$qry);
$data = array();

while($row=mysql_fetch_assoc($rs)){
    $data[] = array(
                   "id"=>$row['id'],
                   "contact_id"=>$row['contact_id'],
                   "conversion_date"=>$row['conversion_date'],
                   "install_cost"=>$row['install_cost'],
                   "monthly_cost"=>$row['monthly_cost']
                );
}
ClosedDBConnection($cn);
echo json_encode($data);