<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 8/12/2015
 * Time: 12:48 PM
 */

require_once "../lib/common.php";
checkSession();
$user_id = $_SESSION['user_id'];

$cn = connectDB();
//var_dump($_REQUEST); exit;

if (isset($_REQUEST)) {
    $contact_id = $_REQUEST["payment_contact_id"];
    $collection_status = $_REQUEST["collection_status"];
}

$is_error = 0;

$select_qry = "select count(*) as `count` from payments where contact_id='$contact_id'";

$result = Sql_exec($cn, $select_qry);

$count = 0;

while ($data = Sql_fetch_array($result)) {
    $count = $data['count'];
}

if (($count == 0 || trim($count) == '0')) {
    $contact_qry = "insert into payments(collection_status,status_update,status_update_by,contact_id,update_date,update_by) values('$collection_status',NOW(), '$user_id','$contact_id', NOW(),'$user_id')";
} else {
    $contact_qry = "update payments set collection_status='$collection_status', status_update=NOW(),status_update_by='$user_id', update_date= NOW(),update_by='$user_id' where contact_id='$contact_id'";
}
try {
    $res = Sql_exec($cn, $contact_qry);
} catch (Exception $e) {
    $is_error = 1;
}

ClosedDBConnection($cn);

echo $is_error;
