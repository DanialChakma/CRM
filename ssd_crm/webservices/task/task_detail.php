<?php
    /**
     * Created by PhpStorm.
     * User: Nazibul
     * Date: 9/1/2015
     * Time: 6:12 PM
     */

    require_once "../lib/common.php";

    $datainfo = (isset($_REQUEST['info'])) ? $_REQUEST['info'] : exit;

    $cn = connectDB();
//var_dump($_SESSION); exit;
    $reslt_data = array();

//$select_qry = "SELECT * FROM work_task left join work_flow_def ON (work_task.work_flow_id = work_flow_def.id) WHERE work_task.id=" . $datainfo['id'];
    $select_qry = "SELECT * FROM work_task left join work_flow_details ON (work_task.member_node_id = work_flow_details.id) LEFT JOIN work_flow_def ON work_flow_def.id=work_flow_details.work_flow_id WHERE work_task.id=" . $datainfo['id'];
//echo $select_qry; exit;
    $res = Sql_exec($cn, $select_qry);

    while ($dt = Sql_fetch_array(($res))) {
        $reslt_data['id'] = $datainfo['id'];
        $reslt_data['task_title'] = $dt['task_title'];
        $reslt_data['task_description'] = $dt['task_description'];
        $reslt_data['progress_report'] = $dt['progress_report'];
        $reslt_data['assign_by'] = $dt['assign_by'];
        $reslt_data['assign_to'] = $dt['assign_to'];
        $reslt_data['user_group_id'] = $dt['user_group_id'];
        $reslt_data['task_status'] = $dt['task_status'];
        $reslt_data['assign_date'] = $dt['assign_date'];
        $reslt_data['due_date'] = $dt['due_date'];
        $reslt_data['update_date'] = $dt['update_date'];
        $reslt_data['node_name'] = $dt['node_name'];
        $reslt_data['work_flow_name'] = $dt['work_flow_name'];
        $work_flow_task = ($dt['task_id'] == '' || $dt['task_id'] == null) ? 0 : $dt['task_id'];//$reslt_data['work_flow_task'] = $dt['task_id'];
        $node_id = ($dt['member_node_id'] == '' || $dt['member_node_id'] == null) ? 0 : $dt['member_node_id'];
    }

    $select_qry2 = "SELECT * FROM contacts JOIN
(SELECT contact_id FROM work_task_contact WHERE work_task_id=" . $datainfo['id'] . ") a
WHERE contacts.id=a.contact_id";


    $select_qry2 = "SELECT contacts.id AS 'id',
  `uid`,
  `doze_id`,
  `customer_type`,
  `create_date`,
  `lead_source`,
  `contact_type`,
  `status`,
  `last_call_date`,
  `final_status`,
  `next_call_date`,
  `do_area`,
  `area`,
  `promoted_to_closed`,
  `promoted_to_customer`,
  `assign_to`,
  `first_name`,
  `last_name`,
  `email`,
  `phone1`,
  `phone2`,
  `address1`,
  `address2`,
  `note`,
  contacts.`update_date`,
  contacts.`update_by`,
  `date_of_birth`,
  `upload_id`,
  `note_id`,
  `stage_id` ,
  (SELECT call_date FROM call_history WHERE call_history.`contact_id`=contacts.`id`   ORDER BY  call_history.id DESC LIMIT 1 ) AS 'call_date',
  work_task_id
  FROM contacts
  INNER JOIN work_task_contact ON work_task_contact.contact_id=contacts.id WHERE work_task_id = " . $datainfo['id'] ;
//echo $select_qry2; exit;
    $res2 = Sql_exec($cn, $select_qry2);
    $i = 0;
    $table_str = '';
    while ($dt = Sql_fetch_array(($res2))) {
//    $j=0;
//    //$reslt_data['table_data'][$i][$j++] = $datainfo['id'];
//    $reslt_data['table_data'][$i][$j++] = $dt['contact_id'];
//    $reslt_data['table_data'][$i][$j++] = $dt['contact_id'];
//    $reslt_data['table_data'][$i][$j++] = $dt['contact_id'];
        if (strtotime($dt['call_date']) > strtotime($reslt_data['assign_date'])) {
            $table_str .= '<tr style="width: 100%; border-bottom: 1px solid #e7e7e7 ; background-color: #5cb85c">';
        } else {
            $table_str .= '<tr style="width: 100%; border-bottom: 1px solid #e7e7e7 ;">';
        }


        $table_str .= '<td style="width: 50px; color: #1b1b1b;">' . $dt['id'] . '</td>';
        $table_str .= '<td style="color: #002a80; width: 30%;"><div style="width: 100%; text-align: left;" onclick="customer_detail_for_this_task(' . $dt['id'] . ',' . $work_flow_task . ',' . $datainfo['id'] . ')"><b><a>' . $dt['first_name'] . ' ' . $dt['last_name'] . '</a></b> (' . $dt['customer_type'] . ') </div>';
        $table_str .= '<div style="color: #444444; font-size: 11px; width: 100%; text-align: right;">' . $dt['email'] . ' (' . $dt['phone1'] . ') </div></td>';
        $table_str .= '<td style="width: 50%"><div>' . $dt['address1'] . '</div></td>';
        $table_str .= '<td style="width: 10%"><div><button class="btn btn-danger" onclick="release_contact_from_task(' . $datainfo['id'] . ',' . $dt['id'] . ')">Release</button></div></td>';
        $table_str .= '</tr>';

        $table_row_id['"' . $dt['id'] . '"'] = $table_str;
        $table_str = '';
    }
    foreach ($table_row_id as $value) {
        $table_str = $table_str . $value;
    }
    $reslt_data['table_data'] = $table_str;

    ClosedDBConnection($cn);

    echo json_encode($reslt_data);
