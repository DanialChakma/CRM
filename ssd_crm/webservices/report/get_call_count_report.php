<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 10/8/2015
 * Time: 10:07 AM
 */

require_once "../lib/common.php";

$cn = connectDB();

if (isset($_REQUEST) && $_REQUEST) {
    $data = $_REQUEST['info'];
    $customer_type = $data['customer_type'];
}

$select_qry = "CALL CALL_COUNT_REPORT('$customer_type')";

$result = Sql_exec($cn, $select_qry);

$data_array = array();
$i = 0;
$serial = 0;
//echo $select_qry; exit;
while ($dt = Sql_fetch_array($result)) {
    $j = 0;
    //$data_array[$i][$j++] = $dt['id'];
    $data_array[$i][$j++] = $dt['phone1'];
    $data_array[$i][$j++] = $dt['name'];
    $data_array[$i][$j++] = $dt['address1'];
    $data_array[$i][$j++] = $dt['email'];
    $data_array[$i][$j++] = $dt['no_call'];
    $data_array[$i][$j++] = $dt['no_days'];
    $data_array[$i][$j++] = $dt['customer_type'];

    $i++;
    $serial++;
}

ClosedDBConnection($cn);


echo json_encode($data_array);