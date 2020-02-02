<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 9/6/2015
 * Time: 2:29 PM
 */

require_once "../lib/common.php";

$datainfo = (isset($_REQUEST)) ? $_REQUEST : exit;

//print_r($datainfo);

$cn = connectDB();
//var_dump($_SESSION); exit;
$reslt_data = array();
$result_data['status'] = false;
$action = '';
$action_key = '';
$action_val = '';
$action_table = '';

$action = $datainfo['action'];
$action_table = $datainfo['action_table'];
if ($action != 'insert') {
    $action_key = $datainfo['action_key'];
    $action_val = $datainfo['action_value'];
}

unset($datainfo['action']);
unset($datainfo['action_key']);
unset($datainfo['action_value']);
unset($datainfo['action_table']);

$qry = '';

if ($action == 'insert') {

    $seperator = '';
    $keys_str = '';
    $values_str = '';

    foreach ($datainfo as $key => $value) {
        //echo $key . ' ' . $value . "\n";
        $keys_str .= $seperator . $key;
        $values_str .= $seperator . "'" . $value . "'";
        $seperator = ', ';
    }

    $qry = "insert into $action_table ( $keys_str ) values ( $values_str )";


} else if ($action == 'update') {

    $update_str = '';
    $seperator = '';
    foreach ($datainfo as $key => $value) {
        //echo $key . ' ' . $value . "\n";
        $update_str .= $seperator . $key . "='" . $value . "'";
        $seperator = ', ';
    }

    $qry = "update $action_table set $update_str where $action_key='$action_val'";

} else if ($action == 'delete') {

    $qry = "delete from $action_table where $action_key='$action_val'";
}

$result_data['query'] = $qry;
try {
    $res = Sql_exec($cn, $qry);

    if ($res) {
        $result_data['status'] = true;
    }
} catch (Exception $e) {
    $result_data['exception'] = $e;
}
ClosedDBConnection($cn);

echo json_encode($result_data);
