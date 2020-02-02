<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 9/9/2015
 * Time: 2:31 PM
 */


require_once "../lib/common.php";


$cn = connectDB();


$login_agent = $_SESSION['user_id'];


$data_array = array();
$i = 0;
if(strtolower($_SESSION['user_role'])=='admin'){
    $task_qry = "SELECT * FROM work_task WHERE ( assign_to = " . $login_agent . " OR " . $login_agent . " IN(SELECT user_id FROM user_group WHERE group_id = (SELECT list_id FROM work_flow_details WHERE id = work_task.member_node_id)) ) AND DATE_FORMAT(due_date, '%Y-%m-%d')= DATE_FORMAT(NOW(), '%Y-%m-%d')";
}else{
    $task_qry = "SELECT * FROM work_task WHERE ( assign_to = " . $login_agent . " ) AND DATE_FORMAT(due_date, '%Y-%m-%d')= DATE_FORMAT(NOW(), '%Y-%m-%d')";
}


$task_res = Sql_exec($cn, $task_qry);

while ($data = Sql_fetch_array(($task_res))) {
    $j = 0;
    $data_array[$i][$j++] = '<div style="width: 5%; float: left; text-align: left;"><b>Today</b></div><div style="float: left; color: #003399; font-weight: bold; padding-right: 10px;" class="task_detail_click" onclick="task_detail(' . $data['id'] . '); return false;"><b>' . $data['task_title'] . '</b></div><div style="float: left; color: #003399; text-align: left;">' . $data['task_description'] . '</div>';
    $i++;
}

$select_qry = "SELECT * FROM call_history WHERE notifyme='yes' AND call_agent=" . $login_agent . " ORDER BY id";

$res = Sql_exec($cn, $select_qry);


while ($dt = Sql_fetch_array(($res))) {
    $j = 0;

    //  $data_array[$i][$j++] = '<a href="#" title="Delete" class="text_green" onclick=\'delete_notification('.$dt['id'].');\'>Delete</a>';
    $data_array[$i][$j++] = '<div  style="width: 5%; padding-right: 20px; float: left;"><input  type="checkbox"  id="notification_' . $dt['id'] . '" value="' . $dt['id'] . '" class="checkbox" /></div>' . ' <div style="float: left; color: #003399; font-weight: bold;" onclick="show_detail_lead(' . $dt['contact_id'] . ')"><a href="#" style="color:#0000ff">' . $dt['feedback'] . '</a></div>';
    //$data_array[$i][$j++] = $dt['feedback'];

    $i++;
}


ClosedDBConnection($cn);

echo json_encode($data_array);

