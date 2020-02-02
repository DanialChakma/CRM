<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 12/18/2015
 * Time: 2:32 PM
 */

require_once "../lib/common.php";

$datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;

$cn = connectDB();
//var_dump($_SESSION); exit;
$reslt_data = array();
$reslt_data['status'] = false;
$reslt_data['msg'] = 'noMsg';

$task_id_select = $datainfo["task_id_select"];
$contact_id_list = $datainfo["contact_id_list"];

$vaues = '';
$seperator = '';
if (trim($contact_id_list) != '' && $contact_id_list != null) {

    $list = array();
    $list = explode('|', $contact_id_list);
    //print_r($list);
    foreach ($list as $idd) {
        $vaues .= $seperator . "('$task_id_select','$idd')";
        $seperator = ',';
    }

    $insert_qry2 = "insert into work_task_contact(`work_task_id`,`contact_id`) values $vaues";
    //echo 'po' . $insert_qry2;

    try {

        $res2 = Sql_exec($cn, $insert_qry2);
        $reslt_data['status'] = true;
        $reslt_data['msg'] = 'Task Updated';

    } catch (Exception $e) {

        $reslt_data['status'] = false;
        $reslt_data['msg'] = $e;
    }
}

ClosedDBConnection($cn);

echo json_encode($reslt_data);