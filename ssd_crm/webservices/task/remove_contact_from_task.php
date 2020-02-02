<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 12/15/2015
 * Time: 5:11 PM
 */
require_once "../lib/common.php";

$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;

$cn = connectDB();
//var_dump($_SESSION); exit;
$reslt_data = array();
$result_data['status'] = false;

$contact_id = $datainfo['contact_id'];
$task_id = $datainfo['task_id'];

$remove_query = "DELETE FROM work_task_contact WHERE work_task_id='$task_id' AND contact_id='$contact_id'";
$result_data['$remove_query'] = $remove_query;
$res = Sql_exec($cn, $remove_query);

if ($res) {
    $result_data['status'] = true;
} else {
    $result_data['status'] = false;
}


ClosedDBConnection($cn);

echo json_encode($result_data);