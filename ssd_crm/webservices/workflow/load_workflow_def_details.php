<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 9/6/2015
 * Time: 11:26 AM
 */

require_once "../lib/common.php";

$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;

$cn = connectDB();
//var_dump($_SESSION); exit;
$reslt_data = array();

$select_qry = "SELECT * FROM work_flow_def WHERE id=" . $datainfo['id'];

$res = Sql_exec($cn, $select_qry);

while ($dt = Sql_fetch_array(($res))) {
    $reslt_data['id'] = $datainfo['id'];
    $reslt_data['work_flow_name'] = $dt['work_flow_name'];
    $reslt_data['description'] = $dt['description'];
    $reslt_data['status'] = $dt['status'];
    $reslt_data['require_days'] = $dt['require_days'];
    $reslt_data['follow_up_before'] = $dt['follow_up_before'];
    $reslt_data['follow_up_mail'] = $dt['follow_up_mail'];
    $reslt_data['notify_over_SMS'] = $dt['notify_over_SMS'];
    $reslt_data['email_text'] = $dt['email_text'];
    $reslt_data['due_date'] = $dt['due_date'];
    $reslt_data['sms_text'] = $dt['sms_text'];
}

ClosedDBConnection($cn);

echo json_encode($reslt_data);