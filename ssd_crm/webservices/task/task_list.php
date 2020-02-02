<?php
/**
 * Created by PhpStorm.
 * User: Nazibul
 * Date: 9/1/2015
 * Time: 12:26 PM
 */

require('../lib/DataTableServerSideCustom.php');

$table = 'work_task';
// Table's primary key
$primaryKey = 'id';
//print_r($_REQUEST['info']);exit;
if ($_REQUEST['info']['role'] == 'Admin') {
    if (isset($_REQUEST['info']['task_status'])) {
        $condition = " task_status='" . $_REQUEST['info']['task_status'] . "' ";
    } else {
        $condition = " 1 ";
    }
} else {
    $condition = " assign_to = '" . $_REQUEST['info']['user_id'] . "' ";
   /* if (isset($_REQUEST['info']['task_status'])) {
        $condition = " task_status='" . $_REQUEST['info']['task_status'] . "' AND (assign_to = '" . $_REQUEST['info']['user_id'] . "' OR '" . $_REQUEST['info']['user_id'] . "' IN(SELECT user_id FROM user_group WHERE group_id = (SELECT list_id FROM work_flow_details WHERE id = work_task.member_node_id))) ";

    } else {
        $condition = " assign_to = '" . $_REQUEST['info']['user_id'] . "' OR '" . $_REQUEST['info']['user_id'] . "' IN(SELECT user_id FROM user_group WHERE group_id = (SELECT list_id FROM work_flow_details WHERE id = work_task.member_node_id)) ";
    }*/
}

$_REQUEST['info']['qryCondition'] = $condition;
    $_REQUEST['info']['qryCondition']=$_REQUEST['info']['qryCondition'].'';
//$_REQUEST['info']['selectString']='molpay_api.id as pri_id, molpay_api.bill_name, molpay_api.amount, molpay_api.tr_status, molpay_api.entry_time, molpay_api.api_response_time, users.id as user_id_dhk';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case object
// parameter names

$columns = array(
    array('db' => 'id', 'dt' => 'filter', 'formatter' => function ($d, $row) {
        if ($d == null || $d == '')
            return ' ';
        else {
            $str = '<div class="list_row">';
            $str .= '<div style="width: 5%; padding-right: 20px; float: left;"><input type="checkbox" value="' . $row['id'] . '"></div>';
            if ($row['task_status'] == 'done') {
                $str .= '<div style="width: 20%; padding-right: 20px; float: left; text-align: right; color: #0a8e03"><b>Done</b></div>';
            }else if($row['task_status'] == 'new'){
                $str .= '<div style="width: 20%; padding-right: 20px; float: left; text-align: right; color: #e43f3f"><b>New</b></div>';
            }else{
                $str .= '<div style="width: 20%; padding-right: 20px; float: left; text-align: right; color: #7465a0"><b>Processing...</b></div>';
            }
            $str .= '<div style="float: left; color: #003399; font-weight: bold;" class="task_detail_click" onclick="task_detail(' . $row['id'] . '); return false;">' . $row['task_title'] . '</div>';
            if ($row['task_status'] != 'done') {
                $str .= '<div style="width: 20px; float: right;" onclick="done_this_task(' . $row['id'] . '); return false;"><span><img src="ssd_crm/img/task11.png" alt="logo" height="20px"></span></div>';
            }
            $str .= '</div>';
            return $str;
        }
    }),
//    array('db' => 'work_id', 'dt' => 'catagory', 'formatter' => function ($d, $row) {
//        if ($d == null || $d == '')
//            return 'catagory';
//        else {
//            return $d;
//        }
//    }),
//    array('db' => 'task_title', 'dt' => 'title', 'formatter' => function ($d, $row) {
//        if ($d == null || $d == '')
//            return ' ';
//        else {
//            return $d;
//        }
//    }),
//    array('db' => 'id', 'dt' => 'action', 'formatter' => function ($d, $row) {
//        if ($d == null || $d == '')
//            return ' ';
//        else {
//            return $d;
//        }
//    })
);
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */
$input_data = isset($_REQUEST) ? $_REQUEST : exit;
$dataTableServer = new DataTableServer();
echo json_encode(
    $dataTableServer->simplePagination($input_data, $table, $columns)
);
