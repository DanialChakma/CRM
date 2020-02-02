<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 9/7/2015
 * Time: 2:45 PM
 */

require_once "../lib/common.php";

$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;

$cn = connectDB();
//var_dump($_SESSION); exit;
$reslt_data = array();

$select_qry = "SELECT * FROM work_flow_details WHERE id=" . $datainfo['id'];

$res = Sql_exec($cn, $select_qry);

while ($dt = Sql_fetch_array(($res))) {
    $reslt_data['id'] = $datainfo['id'];
    $reslt_data['work_flow_id'] = $dt['work_flow_id'];
    $reslt_data['node_name'] = $dt['node_name'];
    $reslt_data['task_id'] = $dt['task_id'];
    $reslt_data['node_id_nxt'] = $dt['node_id_nxt'];
    $reslt_data['list_id'] = $dt['list_id'];
    $reslt_data['approval_type'] = $dt['approval_type'];
    $reslt_data['approval_node_id'] = $dt['approval_node_id'];
    $reslt_data['rejected_node_id'] = $dt['rejected_node_id'];
}

ClosedDBConnection($cn);

echo json_encode($reslt_data);